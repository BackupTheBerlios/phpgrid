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

/**
* JHPCI extensions for barcode rendering
*
* Extends the jh_pdf_barcode with JHPCI methods so it can be
* used together with jh_pdf_table.
*
* @access   public
*/
class jh_pdf_barcode_jhpci extends jh_pdf_barcode
{
    var $width;
    var $height;

    /*
    * Constructor
    *
    * @param    resource   &$pdf   Handle for the PDF document where
    *                              everything should be put onto
    *
    * @access   public
    */
    function jh_pdf_barcode_jhpci(&$pdf)
    {
        $this->jh_pdf_barcode($pdf);
        $this->height=50;
    }

    /*********************************************************************
     Begin of table interface functions
    *********************************************************************/

    /**
    * Set the width for the cell
    *
    * Gets called by the jh_pdf_table class to notify the interface class
    * about the width of the cell where it must place its contents into.
    *
    * @param    float    $width   The width of the cell meassured in PDF
    *                             pixels
    *
    * @access   public
    */
    function jhpci_setwidth($width)
    {
        $this->width=$width;
    }

    /**
    * Set the data for the cell
    *
    * Gets called by the jh_pdf_table class to notify the interface class
    * about the data which it must place into the cell. Please note that
    * the jh_pdf_table class does not care about the type of the data,
    * it is simply passes it through to this function!
    *
    * @param    mixed    $data   The data of the cell
    *
    * @access   public
    */
    function jhpci_setdata($data)
    {
        $this->settext($data);
    }

    /**
    * Get the required height
    *
    * Gets called by the jh_pdf_table class to get the required height
    * of the cell for putting in the contents set by jhpci_setdata()
    * with the width set via jhpci_setwidth().
    *
    * @access   public
    */
    function jhpci_getrequiredheight()
    {
        return $this->height;
    }

    /**
    * Put the data on the PDF document
    *
    * Gets called by the jh_pdf_table class to tell the interface class
    * to put the data onto the PDF document at the passed coordinates.
    *
    * @param    float    $xpos   The left starting position
    * @param    float    $ypos   The top starting position
    *
    * @access   public
    */
    function jhpci_putdata($xpos, $ypos)
    {
        $this->putcode($xpos, $ypos, $this->width, $this->height);
    }

    /*********************************************************************
     Begin of functions extending the functions of the base class
    *********************************************************************/

    /**
    * Set the height of the barcode
    *
    * Sets the height of the barcode irrespective of the width.
    *
    * @param    float    $height   The width of the barcode
    *
    * @access   public
    */
    function setheight($height)
    {
        $this->height=$height;
    }

}

?>
