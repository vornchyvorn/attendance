<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');
$fontPath = __DIR__ . '/tcpdf/fonts/KhmerOSsiemreap.ttf';
$fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
echo " Font registered as: " . $fontname;
?>
