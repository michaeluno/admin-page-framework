<refentry id="{@id}">
  <refnamediv>
    <refname>PHP_Beautifier_Batch documentation</refname>
    <refpurpose>Describe flow of process PHP_Beautifier_Batch </refpurpose>
  </refnamediv>
  <refsynopsisdiv>
    <refsynopsisdivinfo>
      <author>
        <firstname>Claudio</firstname>
        <surname>Bustos</surname>
        <email>cdx@users.sourceforge.com</email>
      </author>
      <author>
        <firstname>Jens</firstname>
        <surname>Bierkandt</surname>
        <email>scortch@users.sourceforge.com</email>
      </author>
    </refsynopsisdivinfo>
    <synopsis></synopsis>
  </refsynopsisdiv>
 <refsect1 id="{@id introduction}">
  <title>Introduction</title>
    <para>PHP_Batch, on {@link PHP_Beautifier_Batch::save()} and {@link PHP_Beautifier_Batch::get()}, call {@link PHP_Beautifier_Batch::getBatchEngine()}. This include a file inside /Beautifier/Batch/ with name equal to mode (File or Directory) plus the compression mode, if exists and create a instance of a subclass of {@link PHP_Beautifier_Batch_Engine}. For example, for output to a file with .gz compress, {@link PHP_Beautifier_Batch::getBatchEngine()} call the file <em>/Beautifier/Batch/FileGz.php</em> and create a instance of <em>PHP_Beautifier_Batch_FilesGz</em>.
    </para>
    <para>The instance of {@link PHP_Beautifier} inside Batch should be called with {@link PHP_Beautifier_Batch::callBeautifier()}. Some commons methods are included in the definition of {@link PHP_Beautifier_Batch_Engine}. </para>
  </refsect1>
</refentry>
