Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K cd ../../ & git archive --format zip --output ../output/admin-page-framework.zip HEAD & cd ../output & unzip admin-page-framework.zip -d ./admin-page-framework"
Set objShell = Nothing