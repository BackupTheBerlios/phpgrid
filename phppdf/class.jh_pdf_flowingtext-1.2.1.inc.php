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

require_once "class.jh_pdf_text-1.2.1.inc.php";

/**
* Class for multi page text output on PDF documents
*
* Extends jh_pdf_text with methods that check, if there is enough
* space left on the current page before putting text on the PDF
* document. If not, a definable function is called which is
* responsible for creating a new page.
*
* @access   public
*/
class jh_pdf_flowingtext extends jh_pdf_text
{
    var $ypos;
    var $lastypos;
    var $firstxpos;
    var $newpagefunction;

    /**
    * Constructor
    *
    * @param    resource   &$pdf        Handle for the PDF document where
    *                                   everything should be put onto
    * @param    float      &$ypos       Variable that stores the current
    *                                   vertical position
    * @param    float      $firstxpos   The left edge of the table
    * @param    float      $lastxpos    The right edge of the table
    * @param    float      $lastypos    Last vertical position; if the
    *                                   text block reaches beyond, the
    *                                   new-page-function which is set
    *                                   via setnewpagefunction() is
    *                                   called
    *
    * @access   public
    *
    * @see      setnewpagefunction()
    */
    function jh_pdf_flowingtext(&$pdf, &$ypos, $firstxpos, $lastxpos,
                                $lastypos)
    {
        $this->jh_pdf_text($pdf);
        $this->setwidth($lastxpos-$firstxpos);
        $this->ypos=&$ypos;
        $this->lastypos=$lastypos;
        $this->firstxpos=$firstxpos;
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
    * Output the text
    *
    * Outputs the text. But before this is done, the remaining space
    * on the current page is checked and a new page will be created
    * by calling the function set via setnewpagefunction() if necessary.
    *
    * @param    string   $alignment   "left"/"right"/"center"
    * @param    float    $leading     The text leading that is used
    *                                 for output
    *
    * @access   public
    */
    function putflowingtext($alignment="left", $leading=0) {
        $requiredspace=$this->getrequiredheight($leading);

        if (($this->ypos-$requiredspace)<$this->lastypos)
            $this->newpage();

        $this->puttext($this->firstxpos, $this->ypos,
                       $alignment, $leading);

        $this->ypos-=$requiredspace;
    }

    /**
    * Begin a new page
    *
    * Calls the function set through setnewpagefunction() to
    * begin a new page.
    *
    * @access   private
    */
    function newpage()
    {
        $newpage=$this->newpagefunction;
        $this->ypos=$newpage();
    }

}

?>
