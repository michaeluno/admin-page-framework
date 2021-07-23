require 'rake'
require 'fileutils'
task :test do
  Dir.chdir(File.dirname(__FILE__)+'/tests')
  system "phpunit ."
  Dir.chdir(File.dirname(__FILE__))
  system "pear package-validate package2.xml"
end

task :coverage do
  Dir.chdir('tests')
  system "phpunit --coverage-html ../site/coverage ."
end

task :install do
    system "pear install package2.xml"
end

task :uninstall do
    system "pear uninstall PHP_Beautifier"
end

task :reinstall => [:uninstall, :install] do
end

task :doc do
    system "phpdoc -c Doc_PHP_Beautifier.ini"
end

task :package do
  FileUtils.mkdir_p "pkg"  
  system "pear package package2.xml"
  system "mv PHP_Beautifier*.tgz pkg"
end
