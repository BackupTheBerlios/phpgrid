<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | jh_php_pdf version 1.2.1                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002 Johann Hanne                                      |
// +----------------------------------------------------------------------+
// | This is free software; you can redistribute it and/or                |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This software is distributed in the hope that it will be useful,     |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this software; if not, write to the               |
// | Free Software Foundation, Inc., 59 Temple Place,                     |
// | Suite 330, Boston, MA  02111-1307 USA                                |
// +----------------------------------------------------------------------+
// | Author: Johann Hanne <jonny@1409.org>                                |
// +----------------------------------------------------------------------+

require_once "class.jh_pdf_barcode-1.2.1.inc.php";

/* Tell the browser that we will send a PDF document */
header("Content-type: application/pdf");

/* Create a PDFlib object */
$pdf=PDF_new();
/* Create a PDF document in memory */
PDF_open_file($pdf, "");

/* Create a DIN A4 page */
PDF_begin_page($pdf, 595, 842);

/* Make an instance of the class */
$barcode=new jh_pdf_barcode(&$pdf);

/* Set the type to 2 of 5 interleaved */
$barcode->settype('25i');

/* Define some test codes */
$code1="1231231230";
$code2="4564564560";
$code3="7897897890";

/* Define a font which will be used to put the plain text
   under the barcode fields */
$font=PDF_findfont($pdf, "Helvetica", 'host', 0);
PDF_setfont($pdf, $font, 12);

/* Set the barcode text and put it onto the PDF document */
$barcode->settext($code1);
$barcode->putcode(30, 812, 100, 50);
/* Put the textual representation under the barcode */
PDF_show_xy($pdf, $code1, 30, 750);

$barcode->settext($code2);
$barcode->putcode(230, 812, 100, 50);
PDF_show_xy($pdf, $code2, 230, 750);

$barcode->settext($code3);
$barcode->putcode(430, 812, 100, 50);
PDF_show_xy($pdf, $code3, 430, 750);

/* Close the last page */
PDF_end_page($pdf);
/* Close the PDF document */
PDF_close($pdf);
/* Output the in-memory created PDF document */
print PDF_get_buffer($pdf);
/* Delete the PDFlib object */
PDF_delete($pdf);

?>
