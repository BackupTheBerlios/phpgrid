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

require_once "class.jh_pdf_barcode_jhpci-1.2.1.inc.php";
require_once "class.jh_pdf_text_jhpci-1.2.1.inc.php";
require_once "class.jh_pdf_container-1.2.1.inc.php";

/* Tell the browser that we will send a PDF document */
header("Content-type: application/pdf");

/* Create a PDFlib object */
$pdf=PDF_new();
/* Create a PDF document in memory */
PDF_open_file($pdf, "");

/* Create a DIN A4 page */
PDF_begin_page($pdf, 595, 842);

/* Create a barcode object and set the type to 2 of 5 interleaved */
$barcode=new jh_pdf_barcode_jhpci(&$pdf);
$barcode->settype('25i');

/* Create a text object which will contain the barcode data as plain text */
$text=new jh_pdf_text_jhpci(&$pdf);
$text->setalignment("center");

/* Create a container object and put the barcode and text objects into it
   with some empty space between them */
$container=new jh_pdf_container(&$pdf);
$container->addobject($barcode);
$container->addemptyspace(5);
$container->addobject($text);
$container->setwidth(100);

/* Define some test codes */
$code1="1231231230";
$code2="4564564560";
$code3="7897897890";

/* Set the barcode text and put it onto the PDF document */
$container->setdata($code1);
$container->putcontainer(30, 812, 100);

$container->setdata($code2);
$container->putcontainer(230, 812, 100);

$container->setdata($code3);
$container->putcontainer(430, 812, 100);

/* Close the last page */
PDF_end_page($pdf);
/* Close the PDF document */
PDF_close($pdf);
/* Output the in-memory created PDF document */
print PDF_get_buffer($pdf);
/* Delete the PDFlib object */
PDF_delete($pdf);

?>
