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

/* Create the first page */
$ypos=newpage(false);

/* Create a text object which will contain the weekday name */
$day=new jh_pdf_text_jhpci(&$pdf);
$day->setalignment("center");

/* Create a barcode object which will contain some number */
$barcode=new jh_pdf_barcode_jhpci(&$pdf);
$barcode->settype('25i');
$barcode->setheight(20);

/* Create a text object which will contain the same number as
   the barcode, but this time as plain text */
$bctext=new jh_pdf_text_jhpci(&$pdf);
$bctext->setalignment("center");

/* Create a container object and put in the three objects
   created above and some empty space between them */
$content=new jh_pdf_container_jhpci(&$pdf);
$content->addobject($day);
$content->addemptyspace(3);
$content->addobject($barcode);
$content->addemptyspace(3);
$content->addobject($bctext);

/* Make an instance of the table class */
$table=new jh_pdf_table(&$pdf, &$ypos, 30, 565, 30);
/* Set a function that will be called if a new page is necessary */
$table->setnewpagefunction(newpage);

/* Define the columns; note that all the columns have "*"
   as their width which means that the whole table width
   will be divided up and assigned equally to the columns */
$col1=&$table->addcolumn("", "*", false, $content);
$col2=&$table->addcolumn("", "*", false, $content);
$col3=&$table->addcolumn("", "*", false, $content);
$col4=&$table->addcolumn("", "*", false, $content);

/* Adjust some table parameters */
$table->settopborderwidth(0);
$table->setbottomborderwidth(0);
$table->setcolspacingwidth(50);
$table->setcolspacingcolor(false);
$table->setrowspacingwidth(10);
$table->setrowspacingcolor(false);
$table->setbgcolors(array(false));

/* Fill up the table cell by cell from the left to the right
   by calling the addcell() method

   The table is entirely made up out of containers, so that each
   cell can contain more than one object (2 text objects and
   1 barcode object in this case)

   Note that the containers consist of 3 objects but only 2
   pieces of data are passed via the array; in this case the
   last element of the array is used for the remaining objects */
$table->addcell(array("Monday", "1111"));
$table->addcell(array("Tuesday", "2222"));
$table->addcell(array("Wednesday", "3333"));
$table->addcell(array("Thursday", "4444"));
$table->addcell(array("Friday", "5555"));
$table->addcell(array("Saturday", "6666"));
$table->addcell(array("Sunday", "7777"));

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
