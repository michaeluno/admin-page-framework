Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K php php-class-inclusion-list-creator.php"
Set objShell = Nothing