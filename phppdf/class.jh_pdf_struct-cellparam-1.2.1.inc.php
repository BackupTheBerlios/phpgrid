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
* This struct enables you to modify the properties of the table cells.
* These properties can be changed at any time and only affect the
* cells that are drawn by calls to addrow() coming after this
* modification. The settings for the table header and the other
* table rows are kept seperately, i.e. you can change header and
* row parameters independently. The cell properties are accessible
* after creation of a column (e.g. $column=$table->addcolumn(...))
* via $column->normcellparam->property_name and
* $column->headcellparam->property_name. The properties should
* be self-explanatory.
*
* @access   private
*/
class cellparam
{
    var $bgcolor;
    var $leftborderwidth;
    var $leftbordercolor;
    var $leftpadding;
    var $rightpadding;
    var $rightborderwidth;
    var $rightbordercolor;
    var $toppadding;
    var $topborderwidth;
    var $topbordercolor;
    var $bottompadding;
    var $bottomborderwidth;
    var $bottombordercolor;
    var $jhpci_class;
}

?>
