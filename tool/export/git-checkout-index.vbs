Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K cd ../../ & git checkout-index -a -f --prefix=../output/admin-page-framework/"
Set objShell = Nothing