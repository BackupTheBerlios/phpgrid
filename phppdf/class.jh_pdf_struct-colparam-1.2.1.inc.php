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
* Struct (class with properties only) for defining cell properties
*
* Used to store column parameters (heading, width, etc.).
*
* @access   private
*/
class colparam
{
    var $heading;
    var $width;
    var $normcellparam;
    var $headcellparam;
    var $eotcallbackfn;
    var $xpos;

    /*
    * Constructor
    */
    function colparam()
    {
        $this->normcellparam=new cellparam();
        $this->headcellparam=new cellparam();
    }
}

?>