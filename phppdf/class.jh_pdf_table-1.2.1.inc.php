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

require_once "class.jh_pdf_base-1.2.1.inc.php";

/*
* This imports the cellparam and colparam structs
* I'd much better like to have them defined in this file,
* but phpdoc requires to have one class per file only :-(
*/
require "class.jh_pdf_struct-cellparam-1.2.1.inc.php";
require "class.jh_pdf_struct-colparam-1.2.1.inc.php";

/**
* Class for easy creation of tables in PDF documents
*
* Somewhat simplifies the creation of table in PDF documents. All you
* have to do is to set the coordinates and the width of the table and
* to define the columns. Cell contents are drawn via a documented
* object orientated interface, so classes for putting in your favourite
* contents can easily be created.
*
* @access   public
*/
class jh_pdf_table extends jh_pdf_base
{
    var $pdf;
    var $ypos;
    var $firstypos;
    var $width;
    var $defaultcolor;
    var $defaultcolparam;
    var $colparam;
    var $colspacingwidth;
    var $colspacingcolor;
    var $rowspacingwidth;
    var $rowspacingcolor;
    var $verttableborderwidth;
    var $verttablebordercolor;
    var $firstxpos;
    var $lastypos;
    var $linecount; // Number of the row on the current page!
    var $newpagefunction;
    var $initialized;
    var $headerinitialized;
    var $hflowtext;
    var $headerheight;
    var $headerbgcolor;
    var $bgcolors;
    var $topborderwidth;
    var $topbordercolor;
    var $afterheaderborderwidth;
    var $afterheaderbordercolor;
    var $bottomborderwidth;
    var $bottombordercolor;
    var $hasheader;
    var $rowbuffer;
    var $forcedrowheight;
    var $cliptocell;

    /**
    * Constructor
    *
    * @param    resource   &$pdf           Handle for the PDF document where
    *                                      everything should be put onto
    * @param    float      &$ypos          Variable that stores the current
    *                                      vertical position
    * @param    float      $firstxpos      The left edge of the table
    * @param    float      $lastxpos       The right edge of the table
    * @param    float      $lastypos       Last vertical position; if the
    *                                      text block reaches beyond, the
    *                                      new-page-function which is set
    *                                      via setnewpagefunction() is
    *                                      called
    * @param    mixed      $defaultcolor   The default foreground color
    *                                      which will be used for elements
    *                                      that have no color applied to
    *                                      them
    *
    * @access   public
    */
    function jh_pdf_table(&$pdf, &$ypos, $firstxpos, $lastxpos, $lastypos,
                          $defaultcolor=0)
    {
        $this->pdf=&$pdf;
        $this->ypos=&$ypos;
        $this->firstypos=0;
        $this->width=$lastxpos-$firstxpos;
        $this->defaultcolor=$defaultcolor;
        $this->colparam=array();
        $this->colspacingwidth=6;
        $this->colspacingcolor=false;
        $this->rowspacingwidth=1;
        $this->rowspacingcolor=false;
        $this->verttableborderwidth=3;
        $this->verttablebordercolor=false;
        $this->firstxpos=$firstxpos;
        $this->lastypos=$lastypos;
        $this->linecount=0;
        $this->initialized=false;
        $this->headerinitialized=false;
        $this->hflowtext=array();
        $this->headerheight=0;
        $this->headerbgcolor=false;
        $this->bgcolors=array(0.9, false);
        $this->topborderwidth=1;
        $this->topbordercolor=$defaultcolor;
        $this->afterheaderborderwidth=1;
        $this->afterheaderbordercolor=$defaultcolor;
        $this->bottomborderwidth=1;
        $this->bottombordercolor=$defaultcolor;
        $this->setdefaultcolparam();
        $this->hasheader=false;
        $this->rowbuffer=array();
        $this->forcedrowheight=false;
        $this->cliptocell=false;
    }

    /**
    * Set built-in default column parameters
    *
    * Set the built-in default column parameters.
    *
    * @access   private
    */
    function setdefaultcolparam()
    {
        $cp=&$this->defaultcolparam;
        $cp->headcellparam->fontface="Helvetica";
        $cp->headcellparam->fontsize=11;
        $cp->headcellparam->fontcolor=$this->defaultcolor;
        $cp->headcellparam->fontparset=false;
        $cp->headcellparam->leading=0;
        $cp->headcellparam->alignment="left";
        $cp->headcellparam->breakmethod="word";
        $cp->headcellparam->leftborderwidth=0;
        $cp->headcellparam->leftbordercolor=$this->defaultcolor;
        $cp->headcellparam->leftpadding=3;
        $cp->headcellparam->rightborderwidth=0;
        $cp->headcellparam->rightbordercolor=$this->defaultcolor;
        $cp->headcellparam->rightpadding=3;
        $cp->headcellparam->topborderwidth=0;
        $cp->headcellparam->topbordercolor=$this->defaultcolor;
        $cp->headcellparam->toppadding=3;
        $cp->headcellparam->bottomborderwidth=0;
        $cp->headcellparam->bottombordercolor=$this->defaultcolor;
        $cp->headcellparam->bottompadding=3;
        $cp->headcellparam->bgcolor=false;
        $cp->normcellparam->fontface="Helvetica";
        $cp->normcellparam->fontsize=11;
        $cp->normcellparam->fontcolor=$this->defaultcolor;
        $cp->normcellparam->fontparset=false;
        $cp->normcellparam->leading=0;
        $cp->normcellparam->alignment="left";
        $cp->normcellparam->breakmethod="word";
        $cp->normcellparam->leftborderwidth=0;
        $cp->normcellparam->leftbordercolor=$this->defaultcolor;
        $cp->normcellparam->leftpadding=3;
        $cp->normcellparam->rightborderwidth=0;
        $cp->normcellparam->rightbordercolor=$this->defaultcolor;
        $cp->normcellparam->rightpadding=3;
        $cp->normcellparam->topborderwidth=0;
        $cp->normcellparam->topbordercolor=$this->defaultcolor;
        $cp->normcellparam->toppadding=3;
        $cp->normcellparam->bottomborderwidth=0;
        $cp->normcellparam->bottombordercolor=$this->defaultcolor;
        $cp->normcellparam->bottompadding=3;
        $cp->normcellparam->bgcolor=false;
    }

    /**
    * Set the background color for the header
    *
    * Sets the background color that is used for the header.
    *
    * @param    mixed     $color   The header background color
    *
    * @access   public
    */
    function setheaderbgcolor($color)
    {
        $this->headerbgcolor=$color;
    }

    /**
    * Set the background colors for the rows
    *
    * Sets the background colors that are used for the rows. The colors
    * must be  passed as an array and the background colors will be
    * alternating beginning with the first color in the array
    * for the first row an each page.
    *
    * @param   array   $colors   The colors for the table rows
    *
    * @access   public
    */
    function setbgcolors($colors)
    {
        $this->bgcolors=$colors;
    }

    /**
    * Set the table's top border width
    *
    * Sets the width of the border which will be drawn on the
    * beginning of the table on each page.
    *
    * @param    float   $width   The width of the border in PDF pixels
    *
    * @access   public
    */
    function settopborderwidth($width)
    {
        $this->topborderwidth=$width;
    }

    /**
    * Set the table's top border color
    *
    * Sets the color of the border which will be drawn on the
    * beginning of the table on each page.
    *
    * @param    mixed   $color   The color of the border
    *
    * @access   public
    */
    function settopbordercolor($color)
    {
        $this->topbordercolor=$color;
    }


    /**
    * Set the table's after-header border width
    *
    * Sets the width of the border which will be drawn after the
    * table header on each page.
    *
    * @param    float   $width   The width of the border in PDF pixels
    *
    * @access   public
    */
    function setafterheaderborderwidth($width)
    {
        $this->afterheaderborderwidth=$width;
    }

    /**
    * Set the table's after-header border color
    *
    * Sets the color of the border which will be drawn after the
    * table header on each page.
    *
    * @param    mixed   $color   The color of the border in PDF pixels
    *
    * @access   public
    */
    function setafterheaderbordercolor($color)
    {
        $this->afterheaderbordercolor=$color;
    }

    /**
    * Set the table's bottom border width
    *
    * Sets the width of the border which will be drawn on the
    * end of the table on each page.
    *
    * @param    float   $width   The width of the border in PDF pixels
    *
    * @access   public
    */
    function setbottomborderwidth($width)
    {
        $this->bottomborderwidth=$width;
    }

    /**
    * Set the table's bottom border color
    *
    * Sets the color of the border which will be drawn on the
    * end of the table on each page.
    *
    * @param    mixed   $color   The color of the border in PDF pixels
    *
    * @access   public
    */
    function setbottombordercolor($color)
    {
        $this->bottombordercolor=$color;
    }

    /**
    * Set the end of page callback function
    *
    * Sets a function which will be called when the end of the
    * page has been reached. It is entirely up to the this function
    * to put some final elements on the current page, create a new
    * page and put elements on it. The function must return the
    * vertical position where the table should be continued.
    *
    * @param    function   $func   The end of page callback function
    *
    * @access   public
    */
    function setnewpagefunction($func)
    {
        $this->newpagefunction=$func;
    }

    /**
    * Set the column spacing width
    *
    * Sets the width of a border which will be drawn between
    * all the columns.
    *
    * @param    float   $width   The width of the inter-column border
    *
    * @access   public
    */
    function setcolspacingwidth($width)
    {
        $this->colspacingwidth=$width;
    }

    /**
    * Set the column spacing color
    *
    * Sets the color of a border which will be drawn between
    * all the columns.
    *
    * @param    mixed   $color   The color of the inter-column border
    *
    * @access   public
    */
    function setcolspacingcolor($color)
    {
        $this->colspacingcolor=$color;
    }

    /**
    * Set the row spacing width
    *
    * Sets the width of a border which will be drawn between
    * all the rows.
    *
    * @param    float   $width   The width of the inter-row border
    *
    * @access   public
    */
    function setrowspacingwidth($width)
    {
        $this->rowspacingwidth=$width;
    }

    /**
    * Set the row spacing color
    *
    * Sets the color of a border which will be drawn between
    * all the rows.
    *
    * @param    mixed   $color   The color of the inter-row border
    *
    * @access   public
    */
    function setrowspacingcolor($color)
    {
        $this->rowspacingcolor=$color;
    }

    /**
    * Set the vertical table border width
    *
    * Sets the width of a border which will be drawn on the left
    * and the right side of the table.
    *
    * @param    float   $width   The width of the vertical table border
    *
    * @access   public
    */
    function setverttableborderwidth($width)
    {
        $this->verttableborderwidth=$width;
    }

    /**
    * Set the vertical table border color
    *
    * Sets the color of a border which will be drawn on the left
    * and the right side of the table.
    *
    * @param    mixed   $color   The color of the vertical table border
    *
    * @access   public
    */
    function setverttablebordercolor($color)
    {
        $this->verttablebordercolor=$color;
    }

    /**
    * Add a column to the table
    *
    * Adds a column to the table. You have to pass the data for the
    * table header, the width and JHPCI (JH PDF container interface)
    * conformant objects which put the actual data on the PDF
    * document.
    * This method will return a colparam object by reference which
    * can be used to modify the column parameters.
    *
    * @param    string   $heading       The data which will be put into the
    *                                   header for this column, usually a text
    *                                   string
    * @param    float    $width         The width of the column in PDF pixels
    * @param    object   $headerjhpci   Instance of a JHPCI conformant class
    *                                   which will be used for putting the
    *                                   header data onto the PDF document
    * @param    object   $normjhpci     Instance of a JHPCI conformant class
    *                                   which will be used for putting the
    *                                   row data onto the document
    *
    * @access   public
    */
    function &addcolumn($heading, $width, $headerjhpci, $normjhpci)
    {
        $cp=new colparam();
        $cp=$this->defaultcolparam;
        $cp->heading=$heading;
        $cp->width=$width;
        $cp->headcellparam->jhpci_class=&$headerjhpci;
        $cp->normcellparam->jhpci_class=&$normjhpci;
        $this->colparam[]=&$cp;

        if ($headerjhpci!==false) {
            $this->hasheader=true;
        }

        return $cp;
    }

    /**
    * Force a certain row height
    *
    * Forces all following rows to be of the specified height.
    *
    * @param    float   $height   The height of the rows
    *
    * @access   public
    */
    function forcerowheight($height)
    {
        $this->forcedrowheight=$height;
    }

    /**
    * Clip to the cell area
    *
    * Clips to the cell area before drawing it, so that its contents
    * will never exceed the boundaries. Useful if you use forcerowheight()
    * or text with wrap method "wordstrict".
    *
    * @param    boolean   $clip   Whether or not to clip to the cell area
    *
    * @access   public
    */
    function cliptocell($clip=true)
    {
        $this->cliptocell=$clip;
    }

    /**
    * Initialization stuff
    *
    * Adjusts the auto-width-column (if used) and calculates
    * the horizontal position of each column. Gets called on
    * the first call of addrow().
    *
    * @access   private
    */
    function initialize()
    {
        // Check, if there are auto-width columns
        $calcwidth=0;
        $autocols=array();
        for ($c=0;$c<sizeof($this->colparam);$c++) {
            if ($this->colparam[$c]->width==="*") {
                array_push($autocols, $c);
            } else {
                $calcwidth+=$this->colparam[$c]->width;
            }
        }

        if (sizeof($autocols)>0) {
            $autowidth=($this->width-
                        ($calcwidth+
                         $this->colspacingwidth*(sizeof($this->colparam)-1)+
                         2*$this->verttableborderwidth)
                       )/sizeof($autocols);

            foreach ($autocols as $autocol) {
                $this->colparam[$autocol]->width=$autowidth;
            }
        }

        // Calculate the horizontal position of each column
        $xpos=$this->firstxpos+$this->verttableborderwidth;
        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $this->colparam[$c]->xpos=$xpos;
            $xpos+=$this->colparam[$c]->width+$this->colspacingwidth;
        }

        $this->initialized=true;
    }

    /*
    * Header initialization
    *
    * Initializes the header parameters.
    *
    * @access   private
    */
    function headerinitialize()
    {
        $maxheight=0;
        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $cp=&$this->colparam[$c];
            $chp=&$cp->headcellparam;

            $chp->jhpci_class->jhpci_setwidth($cp->width-
                                              ($chp->leftborderwidth+
                                               $chp->leftpadding+
                                               $chp->rightborderwidth+
                                               $chp->rightpadding));
            $chp->jhpci_class->jhpci_setdata($cp->heading, 0);
            $height=$chp->jhpci_class->jhpci_getrequiredheight();
            $height+=$chp->toppadding+
                              $chp->topborderwidth+
                              $chp->bottompadding+
                              $chp->bottomborderwidth;
            if ($height>$maxheight) {
                $maxheight=$height;
            }
        }

        $this->headerheight=$maxheight;

        $this->headerinitialized=1;
    }

    /**
    * Put a cell onto the PDF document
    *
    * Puts a cell onto the PDF document including cell border,
    * cell padding and the actual cell data.
    *
    * @param    float              $xpos        The left starting position
    * @param    float              $ypos        The top starting position
    * @param    float              $width       The width of the cell
    * @param    float              $height      The height of the cell
    * @param    object cellparam   $cellparam   Object describing the cell
    *                                           parameters
    *
    * @access   private
    */
    function drawcell($xpos, $ypos, $width, $height, $cellparam, $putdata)
    {
        // When scheduled via $cellparam->bgcolor, fill up the cell with
        // a background color
        if ($cellparam->bgcolor!==false) {
            $this->setcolor($cellparam->bgcolor);
            PDF_setlinewidth($this->pdf, 1);
            PDF_rect($this->pdf, 
                              $xpos+0.5, $this->ypos-$height+0.5, $width-1, $height-1);
            PDF_closepath_fill_stroke($this->pdf);
        }

        $horilinexpos1=$xpos;
        $horilinexpos2=$xpos+$width;

        // When scheduled via $cellparam->topborder{width,color},
        // draw a horizontal line on the top of the cell
        if ($cellparam->topborderwidth>0 &&
            $cellparam->topbordercolor!==false) {
            $borderypos=$ypos-0.5*$cellparam->topborderwidth;
            $this->setcolor($cellparam->topbordercolor);
            PDF_setlinewidth($this->pdf, $cellparam->topborderwidth);
            PDF_moveto($this->pdf, $horilinexpos1, $borderypos);
            PDF_lineto($this->pdf, $horilinexpos2, $borderypos);
            PDF_stroke($this->pdf);
        }

        // When scheduled via $cellparam->bottomborder{width,color},
        // draw a horizontal line on the bottom of the cell
        if ($cellparam->bottomborderwidth>0 &&
            $cellparam->bottombordercolor!==false) {
            $borderypos=$ypos-$height+0.5*$cellparam->bottomborderwidth;
            $this->setcolor($cellparam->bottombordercolor);
            PDF_setlinewidth($this->pdf, $cellparam->bottomborderwidth);
            PDF_moveto($this->pdf, $horilinexpos1, $borderypos);
            PDF_lineto($this->pdf, $horilinexpos2, $borderypos);
            PDF_stroke($this->pdf);
        }

        $vertlineypos1=$this->ypos;
        $vertlineypos2=$this->ypos-$height;

        // When scheduled via $cellparam->leftborder{width,color},
        // draw a vertical line on the left side of the cell
        if ($cellparam->leftborderwidth>0 &&
            $cellparam->leftbordercolor!==false) {
            $borderxpos=$xpos+0.5*$cellparam->leftborderwidth;
            $this->setcolor($cellparam->leftbordercolor);
            PDF_setlinewidth($this->pdf, $cellparam->leftborderwidth);
            PDF_moveto($this->pdf, $borderxpos, $vertlineypos1);
            PDF_lineto($this->pdf, $borderxpos, $vertlineypos2);
            PDF_stroke($this->pdf);
        }

        // When scheduled via $cellparam->rightborder{width,color},
        // draw a vertical line on the right side of the cell
        if ($cellparam->rightborderwidth>0 && $cellparam->rightbordercolor!==false) {
            $borderxpos=$xpos+$width-0.5*$cellparam->rightborderwidth;
            $this->setcolor($cellparam->rightbordercolor);
            PDF_setlinewidth($this->pdf, $cellparam->rightborderwidth);
            PDF_moveto($this->pdf, $borderxpos, $vertlineypos1);
            PDF_lineto($this->pdf, $borderxpos, $vertlineypos2);
            PDF_stroke($this->pdf);
        }

        // Output the cell data
        if ($putdata) {
            $dataxpos=$xpos+$cellparam->leftborderwidth+
                      $cellparam->leftpadding;
            $dataypos=$ypos-$cellparam->toppadding-
                      $cellparam->topborderwidth;

            if ($this->cliptocell) {
                $datawidth=$width-($cellparam->leftborderwidth+
                                   $cellparam->leftpadding+
                                   $cellparam->rightborderwidth+
                                   $cellparam->rightpadding);
                $dataheight=$height-($cellparam->topborderwidth+
                                     $cellparam->toppadding+
                                     $cellparam->bottomborderwidth+
                                     $cellparam->bottompadding);
                PDF_save($this->pdf);
                PDF_rect($this->pdf, $dataxpos, $dataypos-$dataheight, 
                                     $datawidth, $dataheight);
                PDF_clip($this->pdf);
            }

            $cellparam->jhpci_class->jhpci_putdata($dataxpos, $dataypos);

            if ($this->cliptocell) {
                PDF_restore($this->pdf);
            }
        }
    }

    /**
    * Put the table header onto the PDF document
    *
    * Puts the table header onto the PDF document.
    *
    * @access   private
    */
    function putheader()
    {
        if (!$this->headerinitialized) {
            $this->headerinitialize();
        }

        PDF_setlinecap($this->pdf, 0);

        // Background color
        if ($this->headerbgcolor!==false) {
            $this->setcolor($this->headerbgcolor);
            PDF_setlinewidth($this->pdf, 1);
            PDF_rect($this->pdf, 
                     $this->firstxpos+0.5, $this->ypos-$this->headerheight+0.5,
                     $this->width-1, $this->headerheight-1);
            PDF_closepath_fill_stroke($this->pdf);
        }

        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $cp=&$this->colparam[$c];
            $this->drawcell($cp->xpos, $this->ypos, $cp->width,
                            $this->headerheight, $cp->headcellparam, true);
        }

        $this->ypos-=$this->headerheight;

        if ($this->afterheaderborderwidth>0) {
            if ($this->afterheaderbordercolor!==false) {
                PDF_setlinewidth($this->pdf, $this->afterheaderborderwidth);
                $this->setcolor($this->afterheaderbordercolor);
                PDF_moveto($this->pdf, $this->firstxpos,
                           $this->ypos-0.5*$this->afterheaderborderwidth);
                PDF_lineto($this->pdf, $this->firstxpos+$this->width,
                           $this->ypos-0.5*$this->afterheaderborderwidth);
                PDF_stroke($this->pdf);
            }
            $this->ypos-=$this->afterheaderborderwidth;
        }
    }

    /**
    * Put a row onto the PDF document
    *
    * Puts a row onto the PDF document. If there is not enough
    * space left on the page, the function set through
    * setnewpagefunction() is called.
    *
    * @param    array   $dataarray   An array containing one element for
    *                                each column
    *
    * @access   public
    */
    function addrow($dataarray)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        if (sizeof($this->rowbuffer)>0) {
            $rowbuffer=$this->rowbuffer;
            $this->rowbuffer=array();
            $this->addrow($rowbuffer);
        }

        if ($this->forcedrowheight===false) {
            $maxheight=0;
        } else {
            $maxheight=$this->forcedrowheight;
        }

        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $cp=&$this->colparam[$c];
            $cnp=&$cp->normcellparam;

            if (!isset($dataarray[$c])) {
                continue;
            }

            $cnp->jhpci_class->jhpci_setwidth($cp->width-
                                              ($cnp->leftborderwidth+
                                               $cnp->leftpadding+
                                               $cnp->rightborderwidth+
                                               $cnp->rightpadding));
            $cnp->jhpci_class->jhpci_setdata($dataarray[$c], 0);

            if ($this->forcedrowheight===false) {
                $height=$cnp->jhpci_class->jhpci_getrequiredheight();
                $height+=$cnp->toppadding+$cnp->topborderwidth+
                         $cnp->bottompadding+$cnp->bottomborderwidth;
                if ($height>$maxheight) {
                    $maxheight=$height;
                }
            }
        }

        // Check if there is enough space left on the page
        $requiredspace=$maxheight+$this->bottomborderwidth;

        // Do we have to output the header?
        if ($this->linecount==0) {
            $requiredspace+=$this->topborderwidth;

            if ($this->hasheader) {
                $requiredspace+=$this->headerheight+
                                $this->afterheaderborderwidth;
            }
        } else {
            $requiredspace+=$this->rowspacingwidth;
        }

        if (($this->ypos-$requiredspace)<$this->lastypos) {
            $this->newpage();
        }

        // If this is the first row of the table on this page,
        // we first output the top border and the header
        if ($this->linecount==0) {

            $this->firstypos=$this->ypos;

            if ($this->topborderwidth>0) {
                if ($this->topbordercolor!==false) {
                    PDF_setlinecap($this->pdf, 0);
                    PDF_setlinewidth($this->pdf, $this->topborderwidth);
                    $this->setcolor($this->topbordercolor);
                    PDF_moveto($this->pdf, $this->firstxpos,
                               $this->ypos-0.5*$this->topborderwidth);
                    PDF_lineto($this->pdf, $this->firstxpos+$this->width,
                               $this->ypos-0.5*$this->topborderwidth);
                    PDF_stroke($this->pdf);
                }
                $this->ypos-=$this->topborderwidth;
            }

            if ($this->hasheader) {
                $this->putheader();
            }
        }

        if ($this->linecount>0 && $this->rowspacingwidth>0) {
            if ($this->rowspacingcolor!==false) {
                PDF_setlinecap($this->pdf, 0);
                PDF_setlinewidth($this->pdf, $this->rowspacingwidth);
                $this->setcolor($this->rowspacingcolor);
                PDF_moveto($this->pdf, $this->firstxpos,
                           $this->ypos-0.5*$this->rowspacingwidth);
                PDF_lineto($this->pdf, $this->firstxpos+$this->width,
                           $this->ypos-0.5*$this->rowspacingwidth);
                PDF_stroke($this->pdf);
            }
            $this->ypos-=$this->rowspacingwidth;
        }

        // Alternating background color set through $this->bgcolors
        if ($this->bgcolors!==false) {
            $bgcolorinf=each($this->bgcolors);
            if ($bgcolorinf===false) {
                reset($this->bgcolors);
                $bgcolorinf=each($this->bgcolors);
            }
            $bgcolor=$bgcolorinf['value'];
            if ($bgcolor!==false) {
                $this->setcolor($bgcolor);
                PDF_setlinewidth($this->pdf, 1);
                PDF_rect($this->pdf, $this->firstxpos+0.5, $this->ypos-$maxheight+0.5,
                         $this->width-1, $maxheight-1);
                PDF_closepath_fill_stroke($this->pdf);
            }
        }

        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $cp=&$this->colparam[$c];

            if (isset($dataarray[$c])) {
                $putdata=true;
            } else {
                $putdata=false;
            }

            $this->drawcell($cp->xpos, $this->ypos, $cp->width, $maxheight,
                            $cp->normcellparam, $putdata);
        }

        $this->ypos-=$maxheight;

        $this->linecount++;
    }

    /**
    * Add a cell to the table
    *
    * Adds a cell to the table. Each added cell will fill up the
    * current row from the left to the right. If the row is complete,
    * it will be output onto the PDF-document.
    * Uncomplete rows will be output automatically when addrow() or
    * endtable() is called.
    *
    * @param    mixed   $data   The data to put into the cell
    *
    * @access   public
    */
    function addcell($data)
    {
        array_push($this->rowbuffer, $data);
        if (sizeof($this->rowbuffer)==sizeof($this->colparam)) {
            $rowbuffer=$this->rowbuffer;
            $this->rowbuffer=array();
            $this->addrow($rowbuffer);
        }
    }

    /**
    * Begin a new page
    *
    * Calls endtable() to close the table and the function set
    * through setnewpagefunction() to begin a new page.
    *
    * @access   private
    */
    function newpage()
    {
        $this->endtable();
        $newpage=$this->newpagefunction;
        $this->ypos=$newpage();
        $this->linecount=0;
    }

    /**
    * Close the table on the current page
    *
    * Draws the bottom border, the vertical table border, the
    * column spacing border and creates a new page.
    *
    * @access   public
    */
    function endtable()
    {
        if (sizeof($this->rowbuffer)>0) {
            $rowbuffer=$this->rowbuffer;
            $this->rowbuffer=array();
            $this->addrow($rowbuffer);
        }

        if ($this->topborderwidth>0) {
            if ($this->topbordercolor!==false) {
                PDF_setlinecap($this->pdf, 0);
                PDF_setlinewidth($this->pdf, $this->bottomborderwidth);
                $this->setcolor($this->bottombordercolor);
                PDF_moveto($this->pdf, $this->firstxpos,
                           $this->ypos-0.5*$this->bottomborderwidth);
                PDF_lineto($this->pdf, $this->firstxpos+$this->width,
                           $this->ypos-0.5*$this->bottomborderwidth);
                PDF_stroke($this->pdf);
            }
            $this->ypos-=$this->bottomborderwidth;
        }

        // When scheduled via setverttableborderwidth() and
        // setverttablebordercolor(), draw vertical lines on the left and
        // the right side of the table
        if ($this->verttableborderwidth &&
            $this->verttablebordercolor!==false) {
            $this->setcolor($this->verttablebordercolor);
            PDF_setlinewidth($this->pdf, $this->verttableborderwidth);
            PDF_moveto($this->pdf,
                       $this->firstxpos+0.5*$this->verttableborderwidth,
                       $this->firstypos);
            PDF_lineto($this->pdf,
                       $this->firstxpos+0.5*$this->verttableborderwidth,
                       $this->ypos);
            PDF_stroke($this->pdf);
            PDF_moveto($this->pdf,
                       $this->firstxpos+$this->width-
                       0.5*$this->verttableborderwidth,
                       $this->firstypos);
            PDF_lineto($this->pdf,
                       $this->firstxpos+$this->width-
                       0.5*$this->verttableborderwidth,
                       $this->ypos);
            PDF_stroke($this->pdf);
        }

        // When scheduled via setcolspacingwidth() and
        // setcolspacingcolor(), draw lines between the
        // columns
        if ($this->colspacingwidth && $this->colspacingcolor!==false) {
            for ($c=1;$c<sizeof($this->colparam);$c++) {
                $cp=&$this->colparam[$c];

                $borderpos=$cp->xpos-0.5*$this->colspacingwidth;
                $this->setcolor($this->colspacingcolor);
                PDF_setlinewidth($this->pdf, $this->colspacingwidth);
                PDF_moveto($this->pdf, $borderpos, $this->firstypos);
                PDF_lineto($this->pdf, $borderpos, $this->ypos);
                PDF_stroke($this->pdf);
            }
        }

        for ($c=0;$c<sizeof($this->colparam);$c++) {
            $cp=&$this->colparam[$c];
            $cnp=&$cp->normcellparam;
            if ($cp->eotcallbackfn) {
                $fn=$cp->eotcallbackfn;
                $fn($cp->xpos,
                    $cp->xpos+$cnp->leftborderwidth+$cnp->leftpadding,
                    $cp->xpos+$cp->width-($cnp->rightborderwidth+
                                          $cnp->rightpadding),
                    $cp->xpos+$cp->width, $this->ypos);
            }
        }
    }

}

?>
