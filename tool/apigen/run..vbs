Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K apigen --config config.neon"
Set objShell = Nothing