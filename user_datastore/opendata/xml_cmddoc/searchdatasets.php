<?php
$doc = new DOMDocument();
$xsl = new XSLTProcessor();
$doc->load('documentationapi.xsl');
$xsl->importStyleSheet($doc);
$doc->load('searchdatasets.xml');
echo $xsl->transformToXML($doc);
