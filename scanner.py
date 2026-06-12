# pyrefly: ignore [missing-import]
import cv2
import requests
# pyrefly: ignore [missing-import]
from pyzbar.pyzbar import decode
import time
import os
import sys
import signal
import threading
import atexit
import numpy as np
from flask import Flask, Response, request, jsonify
from flask_cors import CORS
import socket

app = Flask(__name__)
CORS(app)  # Mengizinkan web UI mengakses API Flask

# Konfigurasi
API_URL_MASUK = 'http://127.0.0.1:8000/api/scan'
API_URL_PULANG = 'http://127.0.0.1:8000/api/scan-out'
API_KEY = 'YOUR_SECRET_API_KEY'  # Pastikan sama dengan SCANNER_API_KEY di .env Laravel
COOLDOWN_TIME = 3.0  # Detik antar scan untuk QR yang sama

# Load winsound for Windows beep
try:
    import winsound
    def play_success_beep():
        winsound.Beep(1000, 200)

    def play_error_beep():
        winsound.Beep(500, 400)
except ImportError:
    def play_success_beep():
        print("\a", end="")

    def play_error_beep():
        print("\a\a", end="")


class ScannerState:
    def __init__(self):
        self.mode = 'masuk'
        self.last_scans = {}
        self.lock = threading.Lock()
        self.camera_active = True
        self.is_processing = False
        self.processing_text = ""
        self.result_text = ""
        self.result_color = (255, 255, 255)
        self.result_timeout = 0
        self.camera_error = False


state = ScannerState()
cap = None


def release_camera():
    """Release kamera secara global agar tidak terkunci."""
    global cap
    if cap is not None:
        try:
            cap.release()
            print("[OK] Kamera dilepas dengan aman.")
        except Exception:
            pass
        cap = None


def cleanup_and_exit(signum=None, frame=None):
    """Handler untuk signal/atexit - pastikan kamera selalu dilepas."""
    print("[INFO] Membersihkan resource sebelum keluar...")
    state.camera_active = False
    release_camera()
    sys.exit(0)


# Daftarkan handler agar kamera selalu dilepas saat proses mati
atexit.register(release_camera)
signal.signal(signal.SIGTERM, cleanup_and_exit)
signal.signal(signal.SIGINT, cleanup_and_exit)


def find_camera():
    """Coba buka kamera di index 0, 1, 2. Return objek VideoCapture atau None."""
    for index in range(3):
        c = cv2.VideoCapture(index, cv2.CAP_DSHOW)
        if c.isOpened():
            print(f"[OK] Kamera ditemukan di index {index}")
            # Set resolusi agar lebih cepat
            c.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
            c.set(cv2.CAP_PROP_FRAME_HEIGHT, 480)
            return c
        c.release()
    return None


def make_error_frame(message="KAMERA TIDAK TERSEDIA"):
    """Buat frame hitam dengan pesan error untuk ditampilkan saat kamera gagal."""
    frame = np.zeros((480, 640, 3), dtype=np.uint8)
    font = cv2.FONT_HERSHEY_DUPLEX
    text_size = cv2.getTextSize(message, font, 0.9, 2)[0]
    text_x = (640 - text_size[0]) // 2
    text_y = 240
    cv2.putText(frame, message, (text_x, text_y), font, 0.9, (0, 0, 200), 2)
    sub = "Pastikan kamera tidak dipakai aplikasi lain"
    sub_size = cv2.getTextSize(sub, cv2.FONT_HERSHEY_SIMPLEX, 0.5, 1)[0]
    cv2.putText(frame, sub, ((640 - sub_size[0]) // 2, text_y + 40),
                cv2.FONT_HERSHEY_SIMPLEX, 0.5, (150, 150, 150), 1)
    return frame


def send_scan_request(qr_data, mode):
    url = API_URL_MASUK if mode == 'masuk' else API_URL_PULANG
    print(f"\nMendeteksi QR: {qr_data} (Mode: {mode.upper()})")

    try:
        response = requests.post(
            url,
            json={'qr_code': qr_data},
            headers={
                'X-API-Key': API_KEY,
                'Accept': 'application/json'
            },
            timeout=15
        )

        if response.status_code == 200:
            data = response.json()
            with state.lock:
                state.result_text = data.get('message', 'Terjadi kesalahan')
                if data.get('success'):
                    print(f"[BERHASIL] {state.result_text}")
                    state.result_color = (0, 255, 0)
                    play_success_beep()
                else:
                    print(f"[GAGAL] {state.result_text}")
                    state.result_color = (0, 0, 255)
                    play_error_beep()
                state.result_timeout = time.time() + 2.5
        else:
            with state.lock:
                state.result_text = f"Error Server: {response.status_code}"
                state.result_color = (0, 0, 255)
                state.result_timeout = time.time() + 2.5
            print(f"[ERROR SERVER] Status Code: {response.status_code}")
            play_error_beep()

    except requests.exceptions.RequestException as e:
        with state.lock:
            state.result_text = "Gagal terhubung ke server"
            state.result_color = (0, 0, 255)
            state.result_timeout = time.time() + 2.5
        print(f"[ERROR KONEKSI] {e}")
        play_error_beep()

    finally:
        with state.lock:
            state.is_processing = False


def generate_frames():
    global cap

    try:
        # Lepas kamera lama jika ada
        if cap is not None:
            cap.release()

        # Coba buka kamera
        cap = find_camera()

        if cap is None or not cap.isOpened():
            print("[ERROR] Tidak bisa membuka kamera. Menampilkan frame error.")
            state.camera_error = True
            # Terus stream error frame agar browser tidak error
            while state.camera_active:
                error_frame = make_error_frame()
                ret, buffer = cv2.imencode('.jpg', error_frame)
                if ret:
                    yield (b'--frame\r\n'
                           b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')
                time.sleep(0.5)
            return

        state.camera_error = False
        retry_count = 0

        while state.camera_active:
            success, frame = cap.read()
            if not success:
                print("Gagal membaca frame kamera, mencoba lagi...")
                time.sleep(0.5)
                retry_count += 1
                if retry_count > 10:
                    print("Kamera tidak merespon setelah 10 percobaan. Menampilkan frame error.")
                    # Stream error frame dan coba re-init kamera
                    error_frame = make_error_frame("KONEKSI KAMERA TERPUTUS")
                    ret, buffer = cv2.imencode('.jpg', error_frame)
                    if ret:
                        yield (b'--frame\r\n'
                               b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')

                    # Coba buka ulang kamera
                    cap.release()
                    time.sleep(2)
                    cap = find_camera()
                    retry_count = 0
                    if cap is None:
                        time.sleep(3)
                continue

            retry_count = 0

            # Decode QR code
            decoded_objects = decode(frame)
            current_time = time.time()

            for obj in decoded_objects:
                qr_data = obj.data.decode('utf-8')

                # Gambar kotak di sekitar QR code
                pts = obj.polygon
                if len(pts) == 4:
                    pts = [(p.x, p.y) for p in pts]
                    cv2.polylines(frame, [np.array(pts, np.int32)], True, (0, 255, 0), 3)

                # Cooldown scan
                with state.lock:
                    last_time = state.last_scans.get(qr_data, 0)
                    if current_time - last_time > COOLDOWN_TIME and not state.is_processing:
                        state.last_scans[qr_data] = current_time
                        state.is_processing = True
                        state.processing_text = qr_data

                        try:
                            import winsound
                            winsound.Beep(1500, 50)
                        except:
                            pass

                        threading.Thread(
                            target=send_scan_request,
                            args=(qr_data, state.mode),
                            daemon=True
                        ).start()

            # Overlay "MEMPROSES"
            with state.lock:
                if state.is_processing:
                    overlay = frame.copy()
                    cv2.rectangle(overlay, (0, 0), (frame.shape[1], frame.shape[0]), (0, 0, 0), -1)
                    cv2.addWeighted(overlay, 0.6, frame, 0.4, 0, frame)
                    text = "MEMPROSES..."
                    font = cv2.FONT_HERSHEY_DUPLEX
                    text_size = cv2.getTextSize(text, font, 1.2, 2)[0]
                    text_x = (frame.shape[1] - text_size[0]) // 2
                    text_y = (frame.shape[0] + text_size[1]) // 2
                    cv2.putText(frame, text, (text_x, text_y), font, 1.2, (0, 255, 255), 2)
                    cv2.putText(frame, state.processing_text,
                                (text_x + 20, text_y + 40),
                                cv2.FONT_HERSHEY_SIMPLEX, 0.6, (255, 255, 255), 1)

                elif current_time < state.result_timeout:
                    overlay = frame.copy()
                    cv2.rectangle(overlay, (0, frame.shape[0] - 80),
                                  (frame.shape[1], frame.shape[0]), (0, 0, 0), -1)
                    cv2.addWeighted(overlay, 0.7, frame, 0.3, 0, frame)
                    font = cv2.FONT_HERSHEY_SIMPLEX
                    font_scale = 0.7
                    text_size = cv2.getTextSize(state.result_text, font, font_scale, 2)[0]
                    if text_size[0] > frame.shape[1] - 20:
                        font_scale = 0.55
                        text_size = cv2.getTextSize(state.result_text, font, font_scale, 2)[0]
                    text_x = max(10, (frame.shape[1] - text_size[0]) // 2)
                    text_y = frame.shape[0] - 30
                    cv2.putText(frame, state.result_text, (text_x, text_y),
                                font, font_scale, state.result_color, 2)

            # Encode frame ke JPEG
            ret, buffer = cv2.imencode('.jpg', frame)
            if ret:
                yield (b'--frame\r\n'
                       b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')

    finally:
        # Pastikan kamera SELALU dilepas, apapun yang terjadi
        print("[INFO] Generator berhenti, melepaskan kamera...")
        if cap is not None:
            cap.release()
            cap = None


@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')


@app.route('/set_mode', methods=['POST'])
def set_mode():
    data = request.json
    with state.lock:
        state.mode = data.get('mode', 'masuk')
    return jsonify({"success": True, "mode": state.mode})


@app.route('/status', methods=['GET'])
def status():
    with state.lock:
        return jsonify({
            "status": "running",
            "mode": state.mode,
            "is_processing": state.is_processing,
            "camera_error": state.camera_error
        })


@app.route('/health', methods=['GET'])
def health():
    """Endpoint ringan untuk polling dari frontend - konfirmasi server sudah siap."""
    return jsonify({"ok": True})


@app.route('/shutdown', methods=['POST'])
def shutdown():
    """Matikan server dengan melepaskan kamera terlebih dahulu."""
    print("[INFO] Shutdown diminta - melepaskan kamera...")
    state.camera_active = False
    release_camera()
    # Gunakan threading untuk shutdown agar response hin bisa dikirim dulu
    threading.Thread(target=lambda: os._exit(0), daemon=True).start()
    return jsonify({"success": True, "message": "Scanner dimatikan."})


def main():
    print("Memulai Python Scanner Server di http://127.0.0.1:5000 ...")
    try:
        app.run(host='127.0.0.1', port=5000, threaded=True, use_reloader=False)
    except OSError as e:
        print(f"[FATAL] Port 5000 sudah digunakan: {e}")
        release_camera()
        sys.exit(1)


if __name__ == '__main__':
    main()
