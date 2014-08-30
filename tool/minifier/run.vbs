Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K php php-class-files-minifier.php"
Set objShell = Nothing