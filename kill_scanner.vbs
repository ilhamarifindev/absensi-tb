Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd /c FOR /F ""tokens=5"" %a IN ('netstat -aon ^| findstr :5000 ^| findstr LISTENING') DO taskkill /F /PID %a > NUL 2>&1", 0, True
WScript.Sleep 1000