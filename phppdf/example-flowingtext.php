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

require_once "class.jh_pdf_flowingtext-1.2.1.inc.php";

/* Some example text from http://www.kernel.org */

$exampletext=
"What is Linux?

Linux is a clone of the operating system Unix, written from scratch by Linus
Torvalds with assistance from a loosely-knit team of hackers across the Net.
It aims towards POSIX and Single UNIX Specification compliance.

It has all the features you would expect in a modern fully-fledged Unix,
including true multitasking, virtual memory, shared libraries, demand loading,
shared copy-on-write executables, proper memory management, and TCP/IP
networking.

Linux was first developed for 32-bit x86-based PCs (386 or higher). These
days it also runs on (at least) the Compaq Alpha AXP, Sun SPARC and
UltraSPARC, Motorola 68000, PowerPC, PowerPC64, ARM, Hitachi SuperH,
IBM S/390, MIPS, HP PA-RISC, Intel IA-64, DEC VAX, AMD x86-64 and CRIS
architectures.

Linux is easily portable to most general-purpose 32- or 64-bit architectures
as long as they have a paged memory management unit (PMMU) and a port of the
GNU C compiler (gcc).

New to Linux?

If you're new to Linux, you don't want to download the kernel, which is just
a component in a working Linux system. Instead, you want what is called a
distribution of Linux, which is a complete Linux system. There are numerous
distributions available for download on the Internet as well as for purchase
from various vendors; some are general-purpose, and some are optimized for
specific uses. We currently have mirrors of the Debian and RedHat
general-purpose distributions available at mirrors.kernel.org, as well as a
small collection of special-purpose distributions at
http://www.kernel.org/pub/dist/.

Note, however, that most distributions are very large, so unless you have a
very fast Internet link you may want to save yourself some hassle and purchase
a CD-ROM with a distribution; such CD-ROMs are available from a number of
vendors.

The Linux Installation HOWTO has more information how to set up your first
Linux system.
";

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

/* Split the example text into its lines */
$examplelines=split("\n", $exampletext);

/* Make an instance of the class */
$text=new jh_pdf_flowingtext(&$pdf, &$ypos, 30, 565, 30);
/* Set a function that will be called if a new page is necessary */
$text->setnewpagefunction(newpage);
/* Set the font size */
$text->setfontsize(26);

/* Loop through the lines of the example text */
foreach($examplelines as $line) {
    if ($line!="") {
        /* If a line is non-empty, just feed it to the
           jh_pdf_flowingtext class */
        $text->addtext($line, true);
    } else {
        /* If a line is empty, interpret it as end-of-paragraph:
           output the text and leave some empty space */
        $text->putflowingtext();
        $ypos-=26;
    }
}

/* Close the last page */
PDF_end_page($pdf);
/* Close the PDF document */
PDF_close($pdf);
/* Output the current part of the in-memory created PDF document */
print PDF_get_buffer($pdf);
/* Delete the PDFlib object */
PDF_delete($pdf);

?>
