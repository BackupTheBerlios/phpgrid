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

/**
* Class for putting several objects into a box, each below the other
*
* Used to define several objects (e.g. text paragraphs, barcodes)
* which then can be put into a predefined place on the PDF document
* which each object below the other. The objects must be JHPCI compatible.
* Mainly used to put more than one object into a table cell.
*
* @access   public
*/
class jh_pdf_container extends jh_pdf_base
{
    var $pdf;
    var $objects;
    var $data;
    var $width;
    var $yoffsets;
    var $layoutvalid;
    var $requiredheight;

    /**
    * Constructor
    *
    * @param    resource   &$pdf           Handle for the PDF document where
    *                                      everything should be put onto
    *
    * @access   public
    */
    function jh_pdf_container(&$pdf)
    {
        $this->pdf=&$pdf;
        $this->objects=array();
        $this->data=array();;
        $this->width=false;
        $this->yoffsets=array();
        $this->layoutvalid=false;
        $this->requiredheight=false;
    }

    function addobject($obj) {
        array_push($this->objects, $obj);
        $this->layoutvalid=false;
    }

    function addemptyspace($width) {
        array_push($this->objects, $width);
        $this->layoutvalid=false;
    }

    function setdata($data) {
        $this->data=$data;
        $this->layoutvalid=false;
    }

    function setwidth($width) {
        $this->width=$width;
        $this->layoutvalid=false;
    }

    function getrequiredheight() {
        if($this->layoutvalid) {
            return $this->requiredheight;
        }

        $this->yoffsets=array();
        $yoffset=0;
        $j=0;
        for($i=0;$i<sizeof($this->objects);$i++) {
            $obj=&$this->objects[$i];

            if(is_object($obj)) {
                array_push($this->yoffsets, $yoffset);
                $obj->jhpci_setwidth($this->width);
                if(!is_array($this->data)) {
                    $dat=$this->data;
                } else {
                    if($j<sizeof($this->data)) {
                        $dat=$this->data[$j];
                    } else {
                        $dat=$this->data[sizeof($this->data)-1];
                    }
                }
                $obj->jhpci_setdata($dat);
                $yoffset+=$obj->jhpci_getrequiredheight();
                $j++;
            } else {
                $yoffset+=$obj;
            }
        }

        $this->requiredheight=$yoffset;

        $this->layoutvalid=true;
        return $this->requiredheight;
    }

    function putcontainer($xpos, $ypos) {
        $this->getrequiredheight();

        $j=0;
        for($i=0;$i<sizeof($this->objects);$i++) {
            $obj=&$this->objects[$i];

            if(is_object($obj)) {
                $obj->jhpci_putdata($xpos, $ypos-$this->yoffsets[$j]);
                $j++;
            }
        }
    }

}

?>
