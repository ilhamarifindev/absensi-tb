Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd /c python """ & WScript.Arguments(0) & """", 0, False