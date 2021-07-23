@ECHO OFF
@SET CLASSPATH=%CLASSPATH%;I:\java\classpath\resolver-1.0.jar
@ECHO ON
java com.icl.saxon.StyleSheet -x org.apache.xml.resolver.tools.ResolvingXMLReader -y org.apache.xml.resolver.tools.ResolvingXMLReader  -r org.apache.xml.resolver.tools.CatalogResolver -u  -o per_ter.xml file:persona_terapeuta.xhtml file:I:/docbook/html2docbook.xsl