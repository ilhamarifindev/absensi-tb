Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd /c python """ & WScript.Arguments(0) & """ > NUL 2>&1", 0, False