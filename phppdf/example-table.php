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

require_once "class.jh_pdf_text_jhpci-1.2.1.inc.php";
require_once "class.jh_pdf_barcode_jhpci-1.2.1.inc.php";
require_once "class.jh_pdf_container_jhpci-1.2.1.inc.php";
require_once "class.jh_pdf_table-1.2.1.inc.php";

/* Example table data from http://freshmeat.net/stats/
   The 4th column is just a 4-digit consecutive number only
   for demonstrating how to create a barcode column */
$exampletable=array(
array("GNU General Public License (GPL)", "13,000", "64.98%", "0001"),
array("GNU Lesser General Public License (LGPL)", "1,105", "5.52%", "0002"),
array("BSD License", "1,037", "5.18%", "0003"),
array("Freely Distributable", "776", "3.88%", "0004"),
array("Freeware", "660", "3.30%", "0005"),
array("Artistic License", "487", "2.43%", "0006"),
array("Free for non-commercial use", "427", "2.13%", "0007"),
array("OSI Approved", "420", "2.10%", "0008"),
array("Free To Use But Restricted", "364", "1.82%", "0009"),
array("Public Domain", "338", "1.69%", "0010"),
array("Other/Proprietary License with Free Trial", "250", "1.25%", "0011"),
array("Other/Proprietary License", "238", "1.19%", "0012"),
array("MIT/X Consortium License", "188", "0.94%", "0013"),
array("Shareware", "145", "0.72%", "0014"),
array("The Apache License", "145", "0.72%", "0015"),
array("Other/Proprietary License with Source", "127", "0.63%", "0016"),
array("Mozilla Public License (MPL)", "99", "0.49%", "0017"),
array("GNU Free Documentation License (FDL)", "30", "0.15%", "0018"),
array("The PHP License", "23", "0.11%", "0019"),
array("Perl License", "22", "0.11%", "0020"),
array("Python License", "20", "0.10%", "0021"),
array("Eiffel Forum License (EFL)", "18", "0.09%", "0022"),
array("Free For Educational Use", "16", "0.08%", "0023"),
array("zlib/libpng License", "15", "0.07%", "0024"),
array("Q Public License (QPL)", "11", "0.05%", "0025"),
array("IBM Public License", "8", "0.04%", "0026"),
array("Common Public License", "6", "0.03%", "0027"),
array("GNAT Modified GPL (GMGPL)", "5", "0.02%", "0028"),
array("SUN Community Source License", "5", "0.02%", "0029"),
array("Zope Public License (ZPL)", "5", "0.02%", "0030"),
array("SUN Binary Code License", "2", "0.01%", "0031"),
array("SUN Public License", "2", "0.01%", "0032"),
array("The Clarified Artistic License", "2", "0.01%", "0033"),
array("Voxel Public License (VPL)", "2", "0.01%", "0034"),
array("Aladdin Free Public License (AFPL)", "1", "0.00%", "0035"),
array("MITRE Collaborative Virtual Workspace License (CVW)", "1", "0.00%", "0036"),
array("Netscape Public License (NPL)", "1", "0.00%", "0037"),
array("Nokia Open Source License (NOKOS)", "1", "0.00%", "0038"),
array("The Latex Project Public License (LPPL)", "1", "0.00%", "0039"),
array("The Open Content License", "1", "0.00%", "0040"),
array("W3C License", "1", "0.00%", "0041"),
array("Ricoh Source Code Public License", "0", "0.00%", "0042"),
);

/* Tell the browser that we will send a PDF document */
header("Content-type: application/pdf");

/* Create a PDFlib object */
$pdf=PDF_new();
/* Create a PDF document in memory */
PDF_open_file($pdf, "");

/* Function to create a new page; it used for both creating the first
   page and as a callback function for the jh_pdf_flowingtext to close
   the current page and create a new one; the call from jh_pdf_flowingtext
   does not include any parameter, thus the default is set to true */
function newpage($closepage=true) {
    global $pdf;

    if ($closepage) {
        PDF_end_page($pdf);
        /* Output the current part of the in-memory created PDF document */
        print PDF_get_buffer($pdf);
    }

    /* Create a DIN A4 page */
    PDF_begin_page($pdf, 595, 842);

    return 812;
}

$barcode=new jh_pdf_barcode_jhpci(&$pdf);
$barcode->settype('39');

/* Adjust the height of the barcode */
$barcode->setheight(20);

$bctext=new jh_pdf_text_jhpci(&$pdf);
$bctext->setalignment("center");

/* Create the first page */
$ypos=newpage(false);

/* Create instances of JHPCI conformant classes for
   the header and the rows of every column */
$col1h=new jh_pdf_text_jhpci(&$pdf);
$col1r=new jh_pdf_text_jhpci(&$pdf);
$col2h=new jh_pdf_text_jhpci(&$pdf);
$col2r=new jh_pdf_text_jhpci(&$pdf);
$col3h=new jh_pdf_text_jhpci(&$pdf);
$col3r=new jh_pdf_text_jhpci(&$pdf);
/* Note that the 4th column is a barcode column; the header should
   have the text "Barcode" in it, so it's a instance of jh_pdf_text_jhpci
   just like the other columns; however, the rows should show a barcode;
   this is done by just using another class, the rest is the same as
   with the other columns! */
$col4h=new jh_pdf_text_jhpci(&$pdf);
$col4r=new jh_pdf_container_jhpci(&$pdf);

$col4r->addobject($barcode);
$col4r->addemptyspace(3);
$col4r->addobject($bctext);

/* Use Courier for the rows in the 2nd and 3rd column
   and make the content right-aligned */
$col2r->setfontface("Courier");
$col2r->setalignment("right");
$col2h->setalignment("right");
$col3r->setfontface("Courier");
$col3r->setalignment("right");
$col3h->setalignment("right");

/* Make an instance of the table class */
$table=new jh_pdf_table(&$pdf, &$ypos, 30, 565, 30);
/* Set a function that will be called if a new page is necessary */
$table->setnewpagefunction(newpage);

/* Define the columns; the first column has "*" as its width which
   tells the class to use the remaining width for this column;
   the addcolumn() method returns a handle so you can adjust
   the column's parameters */
$col1=&$table->addcolumn("License", "*", &$col1h, &$col1r);
$col2=&$table->addcolumn("Branches", 100, &$col2h, &$col2r);
$col3=&$table->addcolumn("Percentage", 100, &$col3h, &$col3r);
$col4=&$table->addcolumn("Barcode", 100, &$col4h, &$col4r);

/* Adjust some table parameters */
$table->setverttableborderwidth(1);
$table->setverttablebordercolor(0);
$table->setcolspacingwidth(1);
$table->setcolspacingcolor(0);
$table->setrowspacingwidth(1);
$table->setrowspacingcolor(0);
$table->setheaderbgcolor(0.85);
$table->setbgcolors(array(false, 0.95));

/* Adjust the bgcolor of the barcode cell to to white, because
   some barcode readers might get into trouble otherweise */
$col4->normcellparam->bgcolor=1;

/* Adjust the cell padding on the left and the right side of
   the barcode column, so that the table lines don't interfere
   with the barcode */
$col4->normcellparam->leftpadding=10;
$col4->normcellparam->rightpadding=10;

/* Loop through the elements of the example table */
foreach($exampletable as $data) {
    $table->addrow($data);
}

/* Close the table (important!) */
$table->endtable();

/* Close the last page */
PDF_end_page($pdf);
/* Close the PDF document */
PDF_close($pdf);
/* Output the current part of the in-memory created PDF document */
print PDF_get_buffer($pdf);
/* Delete the PDFlib object */
PDF_delete($pdf);

?>
