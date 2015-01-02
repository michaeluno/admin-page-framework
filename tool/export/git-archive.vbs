' Set the script slug. This is used for the file nam and directory name of the output.
Dim sScriptSlug
sScriptSlug = "admin-page-framework"

' Set the command.
Dim sCommand
sCommand = "cmd /K " _ 
 & "cd ../../ & " _
 & "git archive --format zip --output ../output/" & sScriptSlug & ".zip HEAD & " _
 & "cd ../output & " _
 & "unzip -o " & sScriptSlug & ".zip -d ./" & sScriptSlug ' -o is to override

' Run the command
Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run sCommand, 1, true
Set objShell = Nothing