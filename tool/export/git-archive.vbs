Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K cd ../../ & git archive --format zip --output ../admin-page-framework.zip HEAD"
Set objShell = Nothing