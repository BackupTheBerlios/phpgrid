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

$articles=array(
    array("ABC1029384756", "Isolationliner"),
    array("1209348756DEF", "Dustcloth"),
    array("564GHI7382910", "Floorscraper"),
    array("0987JKL612345", "Squeegeehandle"),
    array("0MNO156472983", "Sponge"),
    array("617283940PQR5", "Scouringpad"),
    array("123098STU7654", "Blendmap"),
    array("123VWX7894560", "Mopbucket"),
    array("96875014YZA23", "Rayonmop"),
    array("982BCD1073645", "Dustpan")
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

    return 816.5;
}

/* Create the first page */
$ypos=newpage(false);

/* Create a barcode object which will contain some number */
$barcode=new jh_pdf_barcode_jhpci(&$pdf);
$barcode->settype('128b');
$barcode->setheight(20);

/* Create a text object which will contain the same number as
   the barcode, but this time as plain text */
$bctext=new jh_pdf_text_jhpci(&$pdf);
$bctext->setalignment("center");

/* Create a text object which will contain the article name */
$article=new jh_pdf_text_jhpci(&$pdf);
$article->setalignment("center");

/* Create a container object and put in the three objects
   created above and some empty space between them */
$content=new jh_pdf_container_jhpci(&$pdf);
$content->addobject($barcode);
$content->addemptyspace(3);
$content->addobject($bctext);
$content->addemptyspace(3);
$content->addobject($article);

/* Make an instance of the table class */
$table=new jh_pdf_table(&$pdf, &$ypos, 3, 592, 25.5);
/* Set a function that will be called if a new page is necessary */
$table->setnewpagefunction(newpage);
/* Make the row height static; this is useful for labels
   Bill, is this what you want? ;-) */
$table->forcerowheight(71.9);
/* Hide table contents exceeding the table boundaries */
$table->cliptocell();

/* Define the columns; note that all the columns have "*"
   as their width which means that the whole table width
   will be divided up and assigned equally to the columns */
$col1=&$table->addcolumn("", "*", false, $content);
$col1->normcellparam->leftpadding=25;
$col1->normcellparam->rightpadding=25;
$col1->normcellparam->toppadding=10;
$col1->normcellparam->bottompadding=0;
$col2=&$table->addcolumn("", "*", false, $content);
$col2->normcellparam->leftpadding=25;
$col2->normcellparam->rightpadding=25;
$col2->normcellparam->toppadding=10;
$col2->normcellparam->bottompadding=0;
$col3=&$table->addcolumn("", "*", false, $content);
$col3->normcellparam->leftpadding=25;
$col3->normcellparam->rightpadding=25;
$col3->normcellparam->toppadding=10;
$col3->normcellparam->bottompadding=0;

/* Adjust some table parameters */
$table->settopborderwidth(0);
$table->setbottomborderwidth(0);
$table->setcolspacingwidth(0);
$table->setcolspacingcolor(false);
$table->setrowspacingwidth(0);
$table->setrowspacingcolor(false);
$table->setbgcolors(array(false));
$table->setverttableborderwidth(0);

/* Fill up the table cell by cell from the left to the right
   by calling the addcell() method

   The table is entirely made up out of containers, so that each
   cell can contain more than one object (2 text objects and
   1 barcode object in this case)

   Note that the containers consist of 3 objects but only 2
   pieces of data are passed via the array; in this case the
   last element of the array is used for the remaining objects */
foreach ($articles as $art) {
    $table->addcell(array($art[0], $art[0], $art[1]));
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
