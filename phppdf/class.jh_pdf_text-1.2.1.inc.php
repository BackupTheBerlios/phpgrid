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
* Class for text output on PDF documents
*
* Collects text into a buffer and then puts it onto a PDF document
* with automatic line wrapping. This wrapping can be word or
* character based.
*
* @access   public
*/
class jh_pdf_text extends jh_pdf_base
{
    var $pdf;
    var $width;
    var $currentline;
    var $lines;
    var $linewidths;
    var $newparagraph;

    var $fontface;
    var $fontsize;
    var $fontcolor;
    var $fontparset;
    var $wrapmethod;

    var $buffer;
    var $layoutvalid;

    /**
    * Constructor
    *
    * @param    resource   &$pdf   Handle for the PDF document where
    *                              everything should be put onto
    *
    * @access   public
    */
    function jh_pdf_text(&$pdf)
    {
        $this->pdf=&$pdf;
        $this->width=0;
        $this->wrapmethod="WORD";
        $this->currentline="";
        $this->newparagraph=1;
        $this->fontface="Helvetica";
        $this->fontsize=11;
        $this->fontcolor=false;
        $this->fontparset=false;
        $this->buffer="";
        $this->layoutvalid=false;
    }

    /**
    * Set the text block width
    *
    * Sets the text block width meassured in PDF pixels.
    *
    * @param    float   $width   The width meassured in PDF pixels
    *
    * @access   public
    */
    function setwidth($width)
    {
        $this->width=$width;
        $this->layoutvalid=false;
    }

    /**
    * Set the font face
    *
    * Sets the font face which will be used for the text block.
    *
    * @param    string   $fontface   The font face (e.g. "Helvetica",
    *                                "Times Roman")
    *
    * @access   public
    */
    function setfontface($fontface)
    {
        $this->fontface=$fontface;
        $this->layoutvalid=false;
    }

    /**
    * Set the font size
    *
    * Sets the font size which will be used for the text block.
    *
    * @param    float   $fontsize   The font size
    *
    * @access   public
    */
    function setfontsize($fontsize)
    {
        $this->fontsize=$fontsize;
        $this->layoutvalid=false;
    }

    /**
    * Set the font color
    *
    * Sets the font color which will be used for the text block.
    *
    * @param    mixed   $fontcolor   The font color
    *
    * @access   public
    */
    function setfontcolor($fontcolor)
    {
        $this->fontcolor=$fontcolor;
    }

    /**
    * Set the font parameters
    *
    * Set the fonts parameters which will be used for the text block.
    *
    * @param    array   $parameters   The font parameters which will be set
    *                                 ("underline", "overline", "strikeout",
    *                                 see PDFlib-manual).
    *
    * @access   public
    */
    function setfontparset($parameters)
    {
        $this->fontparset=$parameters;
    }

    /**
    * Set the method for line wrapping
    *
    * Sets the method for line wrapping which will be used for the
    * text block.
    *
    * @param    string   $method   One out of "char", "word" and
    *                              "wordstrict".
    *                              "char" means to wrap the line before the
    *                              first character not fitting into the line.
    *                              "word" means to wrap the line before the
    *                              first word not completely fittig into the
    *                              line. If even one single word does not fit
    *                              into a line, it is wrapped like in "char"
    *                              mode.
    *                              "wordstrict" behaves like "word" except in
    *                              the case where even one single word does
    *                              not fit into a line. In this case the word
    *                              will remain unwrapped, thus reaching beyond
    *                              the specified rectangle.
    *
    * @access   public
    */
    function setwrapmethod($method)
    {
        $method=strtoupper($method);
        if ($method=="CHAR" || $method=="WORD" || $method=="WORDSTRICT") {
            $this->wrapmethod=strtoupper($method);
            $this->layoutvalid=false;
        }
    }

    /**
    * Append a string to the text buffer
    *
    * Appends a string to the current text buffer which will later
    * be output via puttext().
    *
    * @param    string   $text   The string to be added
    * @param    boolean  $sep    Assures that the text will be seperated from
    *                            the current text by a space character
    *
    * @access   public
    */
    function addtext($text, $sep)
    {
        // Seperate width a blank from the preceding text
        if ($sep && $this->buffer!="") {
            $this->buffer.=" ";
        }

        // Remove leading blanks
        $text=ereg_replace("^ *", "", $text);
        // Remove trailing blanks
        $text=ereg_replace(" *$", "", $text);
        // Replace more than one blank in a row by a single blank
        $text=ereg_replace(" +", " ", $text);

        $this->buffer.=$text;
        $this->layoutvalid=false;
    }

    /**
    * Returns the number of required output lines
    *
    * Returns the number of lines which are required when the text
    * is output with the current parameters (font face, font size and
    * wrap method). Note that there will be no performance penalty
    * when calling this method before puttext() as the results
    * are cached in class variables. In fact, this method will
    * be called by puttext() anyway as this is the actual layout
    * engine!
    *
    * @access   public
    */
    function getrequiredlinenum()
    {
        $this->lines=array();
        $this->linewidths=array();

        // Font must be set for PDF_stringwidth()!
        $font=PDF_findfont($this->pdf, $this->fontface, 'host', 0);
        PDF_setfont($this->pdf, $font, $this->fontsize);

        if ($this->wrapmethod=="WORD" || $this->wrapmethod=="WORDSTRICT") {

            // Word orientated line wrapping
            $words=split(" +", $this->buffer);

            $i=0;
            while ($i<sizeof($words)) {

                $currentline="";
                $nextword=$words[$i];
                do {
                    $currentline.=$nextword;
                    $nextword=" ".$words[++$i];
                } while (PDF_stringwidth($this->pdf, $currentline.$nextword)<=
                                $this->width && $i<sizeof($words));

                if (PDF_stringwidth($this->pdf, $currentline)>$this->width) {
                    // Not even one word fits into the line
                    // Wrapping mode "word" means to apply char wrapping,
                    // wrapping mode "wordstrict" means to leave
                    // the word untouched and output it violating the
                    // border
                    if ($this->wrapmethod=="WORD") {
                        $charnum=0;
                        do {
                            $charnum++;
                        } while (pdf_stringwidth($this->pdf, 
                                                 substr($currentline, 0,
                                                 $charnum+1))<=$this->width);

                        // Save the rest of the word for the next line
                        $words[--$i]=substr($currentline, $charnum);

                        // Cut off the line so that it fits in
                        $currentline=substr($currentline, 0, $charnum);
                    }

                }

                $linewidth=pdf_stringwidth($this->pdf, $currentline);

                array_push($this->lines, $currentline);
                array_push($this->linewidths, $linewidth);
            }
        } else {
            // Character orientated line wrapping

            $buf=&$this->buffer;

            $i=0;
            while ($i<strlen($buf)) {

                // Remove leading blanks
                while (substr($buf, $i, 1)==" ") {
                    $i++;
                }

                $charnum=0;
                do {
                    $charnum++;
                } while (pdf_stringwidth($this->pdf, substr($buf, $i, $charnum+1))<=
                                $this->width && ($i+$charnum)<strlen($buf));

                $line=substr($buf, $i, $charnum);

                // Remove trailing blanks
                $line=ereg_replace(" *$", "", $line);

                $linewidth=pdf_stringwidth($this->pdf, $line);

                array_push($this->lines, $line);
                array_push($this->linewidths, $linewidth);

                $i+=$charnum;
            }
        }

        $this->layoutvalid=true;

        return sizeof($this->lines);
    }

    /**
    * Returns the required output height
    *
    * Returns the number of pixels which are required when the text
    * is output with the current parameters and the specified leading.
    *
    * @param    float   $leading   Text leading for which the required
    *                              height should be calculated.
    *
    * @access   public
    */
    function getrequiredheight($leading)
    {
        if (!$this->layoutvalid) {
            $this->getrequiredlinenum();
        }

        if ($leading==0) {
            $leading=$this->fontsize;
        }

        $linenum=sizeof($this->lines);
        $height=$linenum*$this->fontsize+
                ($linenum-1)*($leading-$this->fontsize);
        return $height;
    }

    /**
    * Output the the via the integrated layout engine
    *
    * Outputs the text via the integrated layout engine at the
    * specified coordinates, alignment and leading.
    *
    * @param    float   $xpos        The left starting position
    * @param    float   $ypos        The top starting position
    * @param    string  $alignment   "left"/"right"/"center"
    * @param    float   $leading     The text leading
    *
    * @access   public
    */
    function puttext($xpos, $ypos, $alignment, $leading)
    {
        if (!$this->layoutvalid) {
            $this->getrequiredlinenum();
        }

        $alignment=strtolower($alignment);

        if ($leading==0) {
            $leading=$this->fontsize;
        }

        $linenum=sizeof($this->lines);
        $height=$linenum*$this->fontsize+
                        ($linenum-1)*($leading-$this->fontsize);

        // DEBUG
        //PDF_setcolor($this->pdf, "both", "rgb", 1, 0, 0, 0);
        //PDF_rect($this->pdf, $xpos, $ypos-$height, $this->width, $height);
        //PDF_fill($this->pdf);
        //PDF_setcolor($this->pdf, "both", "rgb", 0, 0, 0, 0);

        $font=PDF_findfont($this->pdf, $this->fontface, 'host', 0);
        PDF_setfont($this->pdf, $font, $this->fontsize);

        PDF_set_parameter($this->pdf, "underline", "false");
        PDF_set_parameter($this->pdf, "overline", "false");
        PDF_set_parameter($this->pdf, "strikeout", "false");

        if (is_array($this->fontparset)) {
            foreach($this->fontparset as $par) {
                PDF_set_parameter($this->pdf, $par, "true");
            }
        }

        // Decrease the vertical position of the text, as the
        // passed x/y-coordinates specify the left top corner
        // of the text whereas PDF_show_xy() uses the coordinates
        // for the text baseline
        $ascender=PDF_get_value($this->pdf, "ascender", $font)*$this->fontsize;
        $ypos-=$ascender+1;

        $this->setcolor($this->fontcolor);

        for ($i=0;$i<sizeof($this->lines);$i++) {
            $line=&$this->lines[$i];
            $linewidth=&$this->linewidths[$i];

            // DEBUG
            //PDF_show_xy($this->pdf, $linewidth, 0, $ypos);

            switch ($alignment) {
            default:
            case "left":
                PDF_show_xy($this->pdf, "$line", $xpos, $ypos);
                break;

            case "right":
                PDF_show_xy($this->pdf, $line, $xpos+$this->width-$linewidth, $ypos);
                break;

            case "center":
                PDF_show_xy($this->pdf, $line,
                            $xpos+($this->width-$linewidth)/2, $ypos);
                break;
            }

            $ypos-=$leading;
        }

        $this->buffer="";
    }

    /**
    * Outputs the the via PDF_show_boxed()
    *
    * Outputs the text via PDF_show_boxed() at the
    * specified coordinates, alignment and leading.
    *
    * @param    float   $xpos        The left starting position
    * @param    float   $ypos        The top starting position
    * @param    string  $alignment   "left"/"right"/"center"
    * @param    float   $leading     The text leading
    *
    * @access   public
    */
    function puttext2($xpos, $ypos, $alignment, $leading)
    {
        if (!$this->layoutvalid) {
            $this->getrequiredlinenum();
        }

        $alignment=strtolower($alignment);

        if ($leading==0) {
            $leading=$this->fontsize;
        }

        $linenum=sizeof($this->lines);
        $height=$linenum*$leading;

        $font=PDF_findfont($this->pdf, $this->fontface, 'host', 0);
        $descender=-PDF_get_value($this->pdf, "descender", $font)*
                              $this->fontsize;

        //PDF_setcolor($this->pdf, "both", "rgb", 0, 0, 1, 0);

        // The passed x/y-coordinates specify the top left corner of the
        // text area whereas PDF_show_boxed() interpretes the coordinates
        // as the x/y-coordinates of the bottom left corner
        $ypos-=$linenum*$leading;

        // PDF_show_boxed() interpretes the coordinates as the text
        // baseline, but we use it for specifying the geometry of the
        // complete  textblock
        $ypos+=$descender;

        // PDF_show_boxed() moves the first text line down by "leading"
        // pixels; we want the first line to really appear at the top of
        // the text block
        $ypos+=($leading-$this->fontsize);

        // In order to allow output via PDF_show_boxed(),
        // lines consisting only of single word (i.e. lines
        // not containing a space), are supplemented by a LF
        // character
        for ($i=0;$i<sizeof($this->lines);$i++) {
            $line=&$this->lines[$i];
            if (strpos($line, " ")===false) {
                $line.="\n";
            }
        }

        $text=implode(" ", $this->lines);
        $text=ereg_replace("\n +", "\n", $text);
        PDF_setfont($this->pdf, $font, $this->fontsize);
        $this->setcolor($this->fontcolor);
        PDF_set_value($this->pdf, "leading", $leading);
        PDF_show_boxed($this->pdf, $text, $xpos, $ypos-1,
                       $this->width, (int)($linenum*$leading)+1,
                       $alignment, "");

        $this->buffer="";
    }

}

?>
