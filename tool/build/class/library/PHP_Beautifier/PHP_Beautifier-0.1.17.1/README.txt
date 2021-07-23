= What is PHP_Beautifier?

PHP_Beautifier is a Open Source PHP aplication, distributed under the terms of PHP Licence 3.0. This program tries to reformat and beautify PHP 4 and PHP 5 code automatically.
Who needs it?

    * developers who get PHP code from other coders and are slightly confused
    * developers who can't read their own PHP code anymore
    * developers who want to share their PHP code

== Little history background

The first version of the program, PhpBeautify, was developed on Php 4 by Jens Bierkandt. In 2003 reached the 'stable' state and is the recomended version for the Php 4 users. This version works almost flawlessy, but was difficult to enhance, and have some problem with strange structures, like pascal-like control instructions and 'switch' structures

With the stabilization of tokenizer and incoming of PHP 5, the need of a new version of the program arise. So, the code for the new version was developed from scratch by Claudio Bustos in 2004, based on the Php Tokenizer and the use of a Plug-in architecture.
Where I can get PHP_Beautifier?

PHP_Beautifier resources are distributed on three sites:

    * PEAR: Download, Change Log and bug report
    * Github: Sourcecode (GIT), wiki and forum

== Features of PHP_Beautifier

    * Version independent: Needs PHP5 to work, but can handle PHP 4 and PHP 5 scripts. Should beautify PHP 3, too (if anyone test it, please send a report)
    * Plataform-independent: Should work on all the plataforms that supports PHP 5. Tested on Windows 98,2000,XP and Linux Gentoo 1.4.6
    * Automatic indentation of PHP source code according to given number of spaces
    * Automatic newlines, if required
    * You can use the web frontend, command line or, if you prefer, could use the class directly
    * Plug-in architecture, by the use of Filters. The control of beautify proccess is delegated on the Filters.
    * The code to beautify can make callbacks to the base class and the filters. So, you can set the options for the beautify inside the same file. See Callbacks
    * Batch processing. You can beautify multiple files inside directories (recursively, if you want to) and save they in another directory.
    * Parse only Php Code. All other tokens (HTML,Comments) are bypassed to the output
    * HEREDOC parsed without any indentation
    * Use of braces for indexing a string (ex. $this->myString{1}) doesn't produce strange indentation
    * Switch statements are indented as spected

== Works with PHP 4 scripts? Will be a PHP 4 version?

This package needs PHP 5 to run, but can handle any PHP file, including PHP 4 and PHP 5 scripts.

In the near future, no. The use of exceptions, overloading & tokenizer - both experimental on PHP 4 - and passing by reference by default for objects could be simulated, but I prefer to focus my effort on PHP 5. But this only affect the installation of the package; you can beautify script written for PHP 4 and PHP 5, without any problem.

== Is secure to use?

The source code of the package beautify itself without any problem. I work with an application with more than 40.000 lines of PHP 4 source code and I did't have any broken file since 0.0.6 version.

The package have a test suite to verify all the important functions. Any bug have a test to verify the fix.

So, IMHO, you can use this application with confidence. Anyway, you always should make regular backups of your files and use some control version system, like CVS or Subversion.