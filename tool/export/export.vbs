''''''''''''''''''''''''''''''''''
' Git Archive Export Script for Windows 1.0.0
' 
''''''''''''''''''''''''''''''''''

' Shell object
Dim oWshShell
Set oWshShell = CreateObject( "WScript.Shell" )

' Set the script slug. This is used for the file name and directory name of the output.
sConfigFilePath             = oWshShell.CurrentDirectory & "/settings.ini"
sScriptSlug                 = ReadIni( sConfigFilePath, "Script", "slug" )

sLocalWorkingCopyDirPath    = Trim ( ReadIni( sConfigFilePath, "Path", "local_working_copy_dir" ) )
sLocalWorkingCopyDirPath    = getFullPath( sLocalWorkingCopyDirPath )
sLocalWorkingCopyDirPathWQ  = chr( 34 ) & sLocalWorkingCopyDirPath & chr( 34 )

sOutputDirPath              = Trim( ReadIni( sConfigFilePath, "Path", "output_dir" ) )
sOutputDirPath              = getFullPath( sOutputDirPath )
sOutputDirPathWQ            = chr( 34 ) & sOutputDirPath & chr( 34 )

' Run the command.
sZipPathWQ  = chr( 34 ) & sOutputDirPath & "\" & sScriptSlug & ".zip" & chr( 34 )
sCommand    = "cmd /c " _ 
    & "cd /d " & sLocalWorkingCopyDirPath & " & " _
    & "git archive --format zip --output " & sZipPathWQ & " HEAD & " _
    & "cd /d " & sOutputDirPathWQ & " & " _
    & "unzip -o " & sScriptSlug & ".zip -d .\" & sScriptSlug 
oWshShell.Run sCommand, 1, true

' Open the output directory in explorer.
sCommand    = "explorer.exe /e, " & sOutputDirPathWQ
oWshShell.Run sCommand, 1, false
Set oWshShell = Nothing


''''''''''''' Functions '''''''''''''''
' Returns the full path.
' @remark       Do not enclose the path in double quotes.
Function getFullPath( sPath )

    Dim oFSO
    Set oFSO = CreateObject( "Scripting.FileSystemObject" )
    getFullPath = oFSO.GetAbsolutePathName( sPath )

End Function

' see http://www.robvanderwoude.com/vbstech_files_ini.php
Function ReadIni( myFilePath, mySection, myKey )
    ' This function returns a value read from an INI file
    '
    ' Arguments:
    ' myFilePath  [string]  the (path and) file name of the INI file
    ' mySection   [string]  the section in the INI file to be searched
    ' myKey       [string]  the key whose value is to be returned
    '
    ' Returns:
    ' the [string] value for the specified key in the specified section
    '
    ' CAVEAT:     Will return a space if key exists but value is blank
    '
    ' Written by Keith Lacelle
    ' Modified by Denis St-Pierre and Rob van der Woude

    Const ForReading   = 1
    Const ForWriting   = 2
    Const ForAppending = 8

    Dim intEqualPos
    Dim objFSO, objIniFile
    Dim strFilePath, strKey, strLeftString, strLine, strSection

    Set objFSO = CreateObject( "Scripting.FileSystemObject" )

    ReadIni     = ""
    strFilePath = Trim( myFilePath )
    strSection  = Trim( mySection )
    strKey      = Trim( myKey )

    If objFSO.FileExists( strFilePath ) Then
        Set objIniFile = objFSO.OpenTextFile( strFilePath, ForReading, False )
        Do While objIniFile.AtEndOfStream = False
            strLine = Trim( objIniFile.ReadLine )

            ' Check if section is found in the current line
            If LCase( strLine ) = "[" & LCase( strSection ) & "]" Then
                strLine = Trim( objIniFile.ReadLine )

                ' Parse lines until the next section is reached
                Do While Left( strLine, 1 ) <> "["
                    ' Find position of equal sign in the line
                    intEqualPos = InStr( 1, strLine, "=", 1 )
                    If intEqualPos > 0 Then
                        strLeftString = Trim( Left( strLine, intEqualPos - 1 ) )
                        ' Check if item is found in the current line
                        If LCase( strLeftString ) = LCase( strKey ) Then
                            ReadIni = Trim( Mid( strLine, intEqualPos + 1 ) )
                            ' In case the item exists but value is blank
                            If ReadIni = "" Then
                                ReadIni = " "
                            End If
                            ' Abort loop when item is found
                            Exit Do
                        End If
                    End If

                    ' Abort if the end of the INI file is reached
                    If objIniFile.AtEndOfStream Then Exit Do

                    ' Continue with next line
                    strLine = Trim( objIniFile.ReadLine )
                Loop
            Exit Do
            End If
        Loop
        objIniFile.Close
    Else
        WScript.Echo strFilePath & " doesn't exists. Exiting..."
        Wscript.Quit 1
    End If
End Function