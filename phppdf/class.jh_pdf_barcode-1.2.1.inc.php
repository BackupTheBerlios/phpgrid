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
* Class for barcode rendering
*
* Renders barcodes directly onto a PDF document using lines
* with different widths.
*
* @access   public
*/
class jh_pdf_barcode extends jh_pdf_base
{
    var $pdf;
    var $text;
    var $type;
    var $asciicode;
    var $code128table=
        array(
        // 0-4
        '212222','222122','222221','121223','121322',
        // 5-9
        '131222','122213','122312','132212','221213',
        // 10-14
        '221312','231212','112232','122132','122231',
        // 15-19
        '113222','123122','123221','223211','221132',
        // 20-24
        '221231','213212','223112','312131','311222',
        // 25-29
        '321122','321221','312212','322112','322211',
        // 30-34
        '212123','212321','232121','111323','131123',
        // 35-39
        '131321','112313','132113','132311','211313',
        // 40-44
        '231113','231311','112133','112331','132131',
        // 45-49
        '113123','113321','133121','313121','211331',
        // 50-54
        '231131','213113','213311','213131','311123',
        // 55-59
        '311321','331121','312113','312311','332111',
        // 60-64
        '314111','221411','431111','111224','111422',
        // 65-69
        '121124','121421','141122','141221','112214',
        // 70-74
        '112412','122114','122411','142112','142211',
        // 75-79
        '241211','221114','413111','241112','134111',
        // 80-84
        '111242','121142','121241','114212','124112',
        // 85-89
        '124211','411212','421112','421211','212141',
        // 90-94
        '214121','412121','111143','111341','131141',
        // 95-99
        '114113','114311','411113','411311','113141',
        // 100-104
        '114131','311141','411131','211412','211214',
        // 105-106
        '211232','2331112'
        );

    /**
    * Constructor
    *
    * @param    resource   &$pdf   Handle for the PDF document where
    *                              everything should be put onto
    *
    * @access   public
    */
    function jh_pdf_barcode(&$pdf) {
        $this->pdf=&$pdf;
        $this->type="39";

        /* Simple check for ASCII environment */
        if (ord('A')==65 && ord('Z')==90 &&
            ord('a')==97 && ord('z')==122 &&
            ord('0')==48 && ord('9')==57) {
            $this->haveascii=true;
        } else {
            $this->haveascii=false;
        }
    }

    /**
    * Set the text
    *
    * Sets the text which should later be output as barcode.
    *
    * @param    string   $text   The string to output as barcode
    *
    * @access   public
    */
    function settext($text) {
        $this->text=$text;
    }

    /**
    * Set the type
    *
    * Sets the barcode type that will be output. Currently
    * supported are code 3 of 9 ("39"), code 2 of 5 interleaved ("25i")
    * and code 128a/b/c ("128a", "128b", "128c")
    *
    * @param    string   $type   The barcode type
    *
    * @access   public
    */
    function settype($type) {
        $this->type=strtolower($type);
    }

    /**
    * Output the barcode
    *
    * Puts the barcode specified via settext() onto the PDF document.
    * The barcode is scaled to fit into the specified space.
    *
    * @param    float    $xpos     The left starting position
    * @param    float    $ypos     The top starting position
    * @param    float    $width    The width of the barcode
    * @param    float    $height   The height of the barcode
    *
    * @access   public
    */
    function putcode($xpos, $ypos, $width, $height) {
        switch($this->type) {

        default:
        case '39':
            // code 39: narrow=4, wide=11, quiet=7
            $narrow=4;
            $wide=11;
            $quiet=7;

            $convtable[0]=$narrow;
            $convtable[1]=$wide;
            $convtable[2]=$quiet;

            $text=$this->text;

            $codes=array();
            array_push($codes, $this->char2code39('START'));
            for ($i=0;$i<strlen($text);$i++) {
                array_push($codes, $this->char2code39(substr($text, $i, 1)));
            }
            array_push($codes, $this->char2code39('STOP'));
            $code=implode('2', $codes);

            // Total width is the sum of:
            // - Widths of the characters' barcode representation (plus
            //   start/stop characters)
            // - Widths of the blank spaces between them
            $fullwidth=(strlen($text)+2)*((6*$narrow)+(3*$wide))+
                       (strlen($text)+1)*$quiet;
            $basewidth=$width/$fullwidth;
            break;

        case '25i':
            $narrow=1;
            $wide=2;

            $convtable[0]=$narrow;
            $convtable[1]=$wide;

            $text=$this->text;

            // The length of the text must be even; if not, we
            // pad it with a leading "0"
            if (strlen($text)%2!=0) {
                $text="0".$text;
            }

            $code=$this->char2code25i('START');
            for ($i=0;$i<strlen($text);$i+=2) {
                $code1=$this->char2code25i(substr($text, $i, 1));
                $code2=$this->char2code25i(substr($text, $i+1, 1));
                // Interleaved code: Shake 'em up!
                for($j=0;$j<5;$j++) {
                    $code.=substr($code1, $j, 1);
                    $code.=substr($code2, $j, 1);
                }
            }
            $code.=$this->char2code25i('STOP');

            // Total width is the sum of:
            // - Widths of the start/stop characters (6*narrow+1*wide)
            // - Widths of the characters' barcode representation
            $fullwidth=strlen($text)*((3*$narrow)+(2*$wide))+
                       6*$narrow+1*$wide;
            $basewidth=$width/$fullwidth;
            break;

        case '128a':
        case '128b':
        case '128c':
            $convtable[1]=1;
            $convtable[2]=2;
            $convtable[3]=3;
            $convtable[4]=4;

            $text=$this->text;

            switch($this->type) {
            case '128a':
                $char2code128index=char2code128aindex;
                break;

            case '128b':
                $char2code128index=char2code128bindex;
                break;

            case '128c':
                $char2code128index=char2code128cindex;
                // The length of the text must be even; if not, we
                // pad it with a leading "0"
                if (strlen($text)%2!=0) {
                    $text="0".$text;
                }
                break;
            }

            $checksum=0;

            $index=$this->$char2code128index('START');
            $code=$this->code128table[$index];
            $checksum+=$index;
            for ($i=0;$i<strlen($text);$i++) {
                if ($this->type=='128c') {
                    // 128c encodes two chars in one go
                    $index=$this->$char2code128index(substr($text, $i, 2));
                    $i++;
                } else {
                    $index=$this->$char2code128index(substr($text, $i, 1));
                }
                $code.=$this->code128table[$index];
                $checksum+=($i+1)*$index;
            }

            // Calculate and output the code 128 checksum
            $checksum%=103;
            $code.=$this->code128table[$checksum];

            $index=$this->$char2code128index('STOP');
            $code.=$this->code128table[$index];

            // Total width is the sum of:
            // - Widths of the start character's barcode representation (11)
            // - Widths of the text characters' barcode representation (11)
            // - Widths of the checksum character's barcode representation (11)
            // - Widths of the stop character's barcode representation (13)
            $fullwidth=(strlen($text)+2)*11+13;
            $basewidth=$width/$fullwidth;
            break;

        }

        $black=true;
        for ($i=0;$i<strlen($code);$i++) {
            $barwidth=$convtable[$code[$i]]*$basewidth;

            if ($black) {
                PDF_setcolor($this->pdf, "both", "gray", 0, 0, 0, 0);
                PDF_setlinecap($this->pdf, 0);
                PDF_setlinewidth($this->pdf, $barwidth);
                PDF_moveto($this->pdf, $xpos+0.5*$barwidth, $ypos);
                PDF_lineto($this->pdf, $xpos+0.5*$barwidth, $ypos-$height);
                PDF_stroke($this->pdf);
            }

            $xpos+=$barwidth;

            $black=!$black;
        }

        return 1;
    }

    /**
    * Convert a character into its code 39 barcode representation
    *
    * Converts a character into a string consisting of 0s and 1s
    * representing its code 39 representation in narrow (0) and wide (1)
    * bars.
    *
    * @param    string    The character to be converted
    *
    * @access   private
    */        
    function char2code39($char) {
        switch ($char) {
        case ' ':
        default:
            return '011000100';
        case '$':
            return '010101000';
        case '%':
            return '000101010';
        case 'START':
        case 'STOP':
            return '010010100';
        case '+':
            return '010001010';
        case '|':
            return '010000101';
        case '.':
            return '110000100';
        case '/':
            return '010100010';
        case '0':
            return '000110100';
        case '1':
            return '100100001';
        case '2':
            return '001100001';
        case '3':
            return '101100000';
        case '4':
            return '000110001';
        case '5':
            return '100110000';
        case '6':
            return '001110000';
        case '7':
            return '000100101';
        case '8':
            return '100100100';
        case '9':
            return '001100100';
        case 'A':
            return '100001001';
        case 'B':
            return '001001001';
        case 'C':
            return '101001000';
        case 'D':
            return '000011001';
        case 'E':
            return '100011000';
        case 'F':
            return '001011000';
        case 'G':
            return '000001101';
        case 'H':
            return '100001100';
        case 'I':
            return '001001100';
        case 'J':
            return '000011100';
        case 'K':
            return '100000011';
        case 'L':
            return '001000011';
        case 'M':
            return '101000010';
        case 'N':
            return '000010011';
        case 'O':
            return '100010010';
        case 'P':
            return '001010010';
        case 'Q':
            return '000000111';
        case 'R':
            return '100000110';
        case 'S':
            return '001000110';
        case 'T':
            return '000010110';
        case 'U':
            return '110000001';
        case 'V':
            return '011000001';
        case 'W':
            return '111000000';
        case 'X':
            return '010010001';
        case 'Y':
            return '110010000';
        case 'Z':
            return '011010000';
        }
    }

    /**
    * Convert a character into its code 25 interleaved barcode representation
    *
    * Converts a character into a string consisting of 0s and 1s
    * representing its code 25 interleaved representation in narrow (0) and
    * wide (1)
    * bars.
    *
    * @param    string    The character to be converted
    *
    * @access   private
    */
    function char2code25i($char) {
        switch ($char) {
        case '0':
            return '00110';
        case '1':
            return '10001';
        case '2':
            return '01001';
        case '3':
            return '11000';
        case '4':
            return '00101';
        case '5':
            return '10100';
        case '6':
            return '01100';
        case '7':
            return '00011';
        case '8':
            return '10010';
        case '9':
            return '01010';
        case 'START':
            return '0000';
        case 'STOP':
            return '100';
        default:
            return '';
        }
    }

    function char2code128aindex($char) {
        // If we are in an ASCII environment, we can calculate the
        // code 128 table offset from the char's ordinal value as
        // code 128A follows the ASCII code
        if ($this->haveascii && strlen($char)==1) {
            $asciicode=ord($char);
            if ($asciicode>=32 && $asciicode<=128) {
                return $asciicode-32;
            } else {
                return 0;
            }
        }

        switch ($char) {
        default:
        case ' ':
            return 0;
        case '!':
            return 1;
        case '"':
            return 2;
        case '#':
            return 3;
        case '$':
            return 4;
        case '%':
            return 5;
        case '&':
            return 6;
        case '\'':
            return 7;
        case '(':
            return 8;
        case ')':
            return 9;
        case '*':
            return 10;
        case '+':
            return 11;
        case '´':
            return 12;
        case '-':
            return 13;
        case '.':
            return 14;
        case '/':
            return 15;
        case '0':
            return 16;
        case '1':
            return 17;
        case '2':
            return 18;
        case '3':
            return 19;
        case '4':
            return 20;
        case '5':
            return 21;
        case '6':
            return 22;
        case '7':
            return 23;
        case '8':
            return 24;
        case '9':
            return 25;
        case ':':
            return 26;
        case ';':
            return 27;
        case '<':
            return 28;
        case '=':
            return 29;
        case '>':
            return 30;
        case '?':
            return 31;
        case '@':
            return 32;
        case 'A':
            return 33;
        case 'B':
            return 34;
        case 'C':
            return 35;
        case 'D':
            return 36;
        case 'E':
            return 37;
        case 'F':
            return 38;
        case 'G':
            return 39;
        case 'H':
            return 40;
        case 'I':
            return 41;
        case 'J':
            return 42;
        case 'K':
            return 43;
        case 'L':
            return 44;
        case 'M':
            return 45;
        case 'N':
            return 46;
        case 'O':
            return 47;
        case 'P':
            return 48;
        case 'Q':
            return 49;
        case 'R':
            return 50;
        case 'S':
            return 51;
        case 'T':
            return 52;
        case 'U':
            return 53;
        case 'V':
            return 54;
        case 'W':
            return 55;
        case 'X':
            return 56;
        case 'Y':
            return 57;
        case 'Z':
            return 58;
        case '[':
            return 59;
        case '\\':
            return 60;
        case ']':
            return 61;
        case '^':
            return 62;
        case '_':
            return 63;
        case 'NUL':
            return 64;
        case 'SOH':
            return 65;
        case 'STX':
            return 66;
        case 'ETX':
            return 67;
        case 'EOT':
            return 68;
        case 'ENQ':
            return 69;
        case 'ACK':
            return 70;
        case 'BEL':
            return 71;
        case 'BS':
            return 72;
        case 'HT':
            return 73;
        case 'LF':
            return 74;
        case 'VT':
            return 75;
        case 'FF':
            return 76;
        case 'CR':
            return 77;
        case 'SO':
            return 78;
        case 'SI':
            return 79;
        case 'DLE':
            return 80;
        case 'DC1':
            return 81;
        case 'DC2':
            return 82;
        case 'DC3':
            return 83;
        case 'DC4':
            return 84;
        case 'NAK':
            return 85;
        case 'SYN':
            return 86;
        case 'ETB':
            return 87;
        case 'CAN':
            return 88;
        case 'EM':
            return 89;
        case 'SUB':
            return 90;
        case 'ESC':
            return 91;
        case 'FS':
            return 92;
        case 'GS':
            return 93;
        case 'RS':
            return 94;
        case 'US':
            return 95;
        case 'START':
            return 103;
        case 'STOP':
            return 106;
        }
    }

    function char2code128bindex($char) {
        // If we are in an ASCII environment, we can calculate the
        // code 128 table offset from the char's ordinal value as
        // code 128B follows the ASCII code
        if ($this->haveascii && strlen($char)==1) {
            $asciicode=ord($char);
            if ($asciicode>=32 && $asciicode<=96) {
                return $asciicode-32;
            } elseif ($asciicode>=0 && $asciicode<=31) {
                return $asciicode+64;
            } else {
                return 0;
            }
        }

        switch ($char) {
        case ' ':
            return 0;
        case '!':
            return 1;
        case '"':
            return 2;
        case '#':
            return 3;
        case '$':
            return 4;
        case '%':
            return 5;
        case '&':
            return 6;
        case '\'':
            return 7;
        case '(':
            return 8;
        case ')':
            return 9;
        case '*':
            return 10;
        case '+':
            return 11;
        case '´':
            return 12;
        case '-':
            return 13;
        case '.':
            return 14;
        case '/':
            return 15;
        case '0':
            return 16;
        case '1':
            return 17;
        case '2':
            return 18;
        case '3':
            return 19;
        case '4':
            return 20;
        case '5':
            return 21;
        case '6':
            return 22;
        case '7':
            return 23;
        case '8':
            return 24;
        case '9':
            return 25;
        case ':':
            return 26;
        case ';':
            return 27;
        case '<':
            return 28;
        case '=':
            return 29;
        case '>':
            return 30;
        case '?':
            return 31;
        case '@':
            return 32;
        case 'A':
            return 33;
        case 'B':
            return 34;
        case 'C':
            return 35;
        case 'D':
            return 36;
        case 'E':
            return 37;
        case 'F':
            return 38;
        case 'G':
            return 39;
        case 'H':
            return 40;
        case 'I':
            return 41;
        case 'J':
            return 42;
        case 'K':
            return 43;
        case 'L':
            return 44;
        case 'M':
            return 45;
        case 'N':
            return 46;
        case 'O':
            return 47;
        case 'P':
            return 48;
        case 'Q':
            return 49;
        case 'R':
            return 50;
        case 'S':
            return 51;
        case 'T':
            return 52;
        case 'U':
            return 53;
        case 'V':
            return 54;
        case 'W':
            return 55;
        case 'X':
            return 56;
        case 'Y':
            return 57;
        case 'Z':
            return 58;
        case '[':
            return 59;
        case '\\':
            return 60;
        case ']':
            return 61;
        case '^':
            return 62;
        case '_':
            return 63;
        case '`':
        case "'":
            return 64;
        case 'a':
            return 65;
        case 'b':
            return 66;
        case 'c':
            return 67;
        case 'd':
            return 68;
        case 'e':
            return 69;
        case 'f':
            return 70;
        case 'g':
            return 71;
        case 'h':
            return 72;
        case 'i':
            return 73;
        case 'j':
            return 74;
        case 'k':
            return 75;
        case 'l':
            return 76;
        case 'm':
            return 77;
        case 'n':
            return 78;
        case 'o':
            return 79;
        case 'p':
            return 80;
        case 'q':
            return 81;
        case 'r':
            return 82;
        case 's':
            return 83;
        case 't':
            return 84;
        case 'u':
            return 85;
        case 'v':
            return 86;
        case 'w':
            return 87;
        case 'x':
            return 88;
        case 'y':
            return 89;
        case 'z':
            return 90;
        case '{':
            return 91;
        case '|':
            return 92;
        case '}':
            return 93;
        case '~':
            return 94;
        case 'DEL':
            return 95;
        case 'START':
            return 104;
        case 'STOP':
            return 106;
        }
    }

    function char2code128cindex($char) {
        // Code 128C has numbers only and each barcode sequence
        // represents two digits; the number expressed through
        // these two digits corresponds to the offset in the
        // code 128 table (i.e. "00" has offset 0, "01" has
        // offset 1, "02" has offset 2 and so on)
        switch ($char) {
        default:
            settype($char, "integer");
            return $char;
        case 'START':
            return 105;
        case 'STOP':
            return 106;
        }
    }

}

?>
