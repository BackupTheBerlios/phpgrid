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

/**
* jh_pdf base class
*
* Basic PDF functions common to all jh_pdf classes
*
* @access   private
*/
class jh_pdf_base
{

    /**
    * Set the color
    *
    * Sets the current color in the PDF document. For valid color formats
    * see the README file.
    *
    * @param    mixed   $color   the color to set
    *
    * @access   private
    */
    function setcolor($color)
    {
        if (!is_array($color)) {
            PDF_setcolor($this->pdf, "both", "gray", $color, 0, 0, 0);
        } else {
            switch (sizeof($color)) {
            case 1:
                PDF_setcolor($this->pdf, "both", "gray", $color[0], 0, 0, 0);
                break;

            case 3:
                PDF_setcolor($this->pdf, "both", "rgb",
                             $color[0], $color[1], $color[2], 0);
                break;

            case 4:
                PDF_setcolor($this->pdf, "both", "cmyk",
                             $color[0], $color[1], $color[2], $color[3]);
                break;
            }
        }
    }

}

?>
