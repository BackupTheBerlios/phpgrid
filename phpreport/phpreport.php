<?php
/*************************************************************************** 
                                                                             
 PHPReport
                                                                             
 Copyright (c) 2002 Security Data S.A. http://www.securitydata.es
                                                                             
 This program is free software; you can redistribute it and/or               
 modify it under the terms of the GNU General Public                         
 License as published by the Free Software Foundation; either                
 version 2 of the License, or (at your option) any later version.            
                                                                             
 This program is distributed in the hope that it will be useful,             
 but WITHOUT ANY WARRANTY; without even the implied warranty of              
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU           
 General Public License for more details.                                    
                                                                             
 You should have received a copy of the GNU General Public License           
 along with this program; see the file COPYING.  If not, write to            
 the Free Software Foundation, Inc., 59 Temple Place - Suite 330,            
 Boston, MA 02111-1307, USA.                                                 
                                                                             
***************************************************************************/

define('DETAIL',1);
define('HEADER',2);

define('HTML',1);
define('PDF',2);

define('PORTRAIT',1);
define('LANDSCAPE',2);

require_once "phppdf/class.jh_pdf_text_jhpci-1.2.1.inc.php";
require_once "phppdf/class.jh_pdf_barcode_jhpci-1.2.1.inc.php";
require_once "phppdf/class.jh_pdf_container_jhpci-1.2.1.inc.php";
require_once "phppdf/class.jh_pdf_table-1.2.1.inc.php";
require_once "phppdf/class.jh_pdf_flowingtext-1.2.1.inc.php";

global $pdfdoc;
global $orientation;
global $current_page;

$current_page=1;


			/* Function to create a new page; it used for both creating the first
			   page and as a callback function for the jh_pdf_flowingtext to close
			   the current page and create a new one; the call from jh_pdf_flowingtext
			   does not include any parameter, thus the default is set to true */
			function newpage($closepage=true) 
			{
				global $pdfdoc, $orientation, $current_page;
				$result=0;
				$rm=0;
				
				if ($closepage) 
				{
					PDF_end_page($pdfdoc);
					/* Output the current part of the in-memory created PDF document */
					print PDF_get_buffer($pdfdoc);
				}

				switch($orientation)
				{
					case LANDSCAPE: 
						{
							/* Create a DIN A4 landscape page */
							PDF_begin_page($pdfdoc, 842,595);	
							$result=565;	
							$rm=812;
							break;
						}
					case PORTRAIT:
						{
							/* Create a DIN A4 landscape page */
							PDF_begin_page($pdfdoc, 595, 842);	
							$result=812;							
							$rm=565;
							break;
						}
				}
				
				if ($current_page!=1)
				{
					// Make an instance of the class
					$text=new jh_pdf_flowingtext(&$pdfdoc, &$result, 30, $rm, 30);

					$text->setfontsize(9);
					$text->addtext("Página $current_page", true);
                    $text->puttext(30,$result,"right",0);

        
					$result-=26;
				}
				$current_page++;
			
				return($result);
			}			
        
		/**
        *PHPGrid - A report generator for PHP, based on PHPGrid
        *
        *This class allows to create reports from PHP
        *
        *@version 0.1
        *@author Security Data S.A. <info@securitydata.es>
        */
        class PHPReport
        {
			var $title='';
			
			var $bands=array();
			
			var $params=array();
			
			var $pdfwidths=array();
			
			var $coltypes=array();
			
			var $pdffontsize=9;
			
			var $filterfunc='';
			
			var $recordcounter=0;
			
			var $headercolor=0;
			
			var $subx=30;
			
			var $showsummaries=TRUE;
			
			var $filtered=FALSE;
			
			var $issubreport=FALSE;
			
			var $outputformat=HTML;
			
			var $orientation=LANDSCAPE;
			
			var $pdf;
			
			var $ypos;
			
			var $table;
			
			var $phpgrid;
			
			function PHPReport($agrid)
			{
				$this->phpgrid=$agrid;
				$this->bands=array('header'=>array('bandtype'=>HEADER),'detail'=>array('bandtype'=>DETAIL));
			}	
			
			function DetailBandHasData($bandparams)
			{
				if (array_key_exists('grid',$bandparams)) $grid=$bandparams['grid'];
				else $grid=$this->phpgrid;
				
				$params=$this->params;
				$subreports=$bandparams['subreports'];
				
				$field='';
				$value='';
				if (count($params)!=0) 
				{
					reset($params);
					list($field,$value)=each($params);
				}
				
				$fieldvalues=$grid->getrecord($field,$value,$this->filtered);			
				
				if (count($fieldvalues)==0) return(FALSE);
				else return(TRUE);
			}
			
			function bandHasData($bandparams)
			{
				$bandtype=$bandparams['bandtype'];
				switch ($bandtype)
				{
					case DETAIL: 
								{
									return($this->DetailBandHasData($bandparams));
									break;
								}
											
					case HEADER: 
								{
									return(FALSE);
									break;
								}								
								
				}			
			}			
			
			function hasData()
			{
					$result=FALSE;
					while (list($bandname,$bandparams)=each($this->bands))
					{
						$result=($result || $this->bandHasData($bandparams));
					}	
					

					return($result);
			}
			
			function pageheader()
			{
			
				if ($this->title!='')
				{
								// Table for titles
								switch($this->orientation)
								{
									case LANDSCAPE: 
									{
										$atable=new jh_pdf_table(&$this->pdf, &$this->ypos, $this->subx, 812, 30);
										break;
									}
									case PORTRAIT: 
									{
										$atable=new jh_pdf_table(&$this->pdf, &$this->ypos, $this->subx, 565, 30);
										break;
									}									
								}
								
//******************************************************
								$fecha=date("d/m/Y h:m:s");
								
								$colh=new jh_pdf_text_jhpci(&$this->pdf);
								$colh->setfontsize(26);						
						
								$colr=new jh_pdf_text_jhpci(&$this->pdf);	
								$colr->setfontsize($this->pdffontsize);												
							
								$tcol=&$atable->addcolumn($this->title, '*', $colh, $colr);
								
								$colh=new jh_pdf_text_jhpci(&$this->pdf);
								$colh->setalignment("right");								
								$colh->setfontsize($this->pdffontsize);						
						
								$colr=new jh_pdf_text_jhpci(&$this->pdf);	
								$colr->setfontsize($this->pdffontsize);	
								
								$dcol=&$atable->addcolumn($fecha."", '*', $colh, $colr);
								
								$this->phpgrid->getlookupquery();
								$what=$this->phpgrid->statustext;

								if ($what=='')
								{
									$what='Mostrando todos los registros';
								}
								
								
								$data=array($what);
								
								$atable->addrow($data);								
								
								$atable->endtable();														
					}
			}
			
		
			function run()
			{
				global $output_format;
				global $form_action;
				global $sbsearch_x;
				global $pdfdoc;
				global $orientation;
				
				if (isset($output_format)) $this->outputformat=$output_format;
				
				
				
				if ((isset($form_action)) || (!$this->filtered))
				{
					reset($this->bands);
					
					if ($this->outputformat==PDF) 
					{
						if (!$this->issubreport)
						{
							header("Content-type: application/pdf");
							/* Create a PDFlib object */
							$this->pdf=PDF_new();
							
							$pdfdoc=$this->pdf;
							$orientation=$this->orientation;
							
							/* Create a PDF document in memory */
							PDF_open_file($this->pdf, "");						
						
							/* Create the first page */
							$this->ypos=newpage(false);						
							
							$this->pageheader();							
						}
						
						/* Make an instance of the table class */
						switch($this->orientation)
						{
							case LANDSCAPE: 
									{
										$this->table=new jh_pdf_table(&$this->pdf, &$this->ypos, $this->subx, 812, 30);
										break;
									}
							case PORTRAIT: 
									{
										$this->table=new jh_pdf_table(&$this->pdf, &$this->ypos, $this->subx, 565, 30);
										break;
									}									
						}
						
						/* Set a function that will be called if a new page is necessary */
						$this->table->setnewpagefunction(newpage);						
						
						if ($this->issubreport)
						{
							$this->table->setverttableborderwidth(1);
							$this->table->setverttablebordercolor(0);
							//$this->table->setcolspacingwidth(1);
							//$this->table->setcolspacingcolor(0);
							$this->table->setrowspacingwidth(1);
							$this->table->setrowspacingcolor(0);
						}
						$this->table->setheaderbgcolor($this->headercolor);
						$this->table->setbgcolors(array(false, 0.90));

					}
					
					
					if ($this->outputformat==HTML)
					{
						echo "<link rel=\"StyleSheet\" href=\"phpreport/report.css\" type=\"text/css\" />\n";
						echo "<style type=\"text/css\">";
						echo "@import url(\"phpreport/report.css\"); ";
						echo "</style>";		 					
					}
					
				
					$w="width=\"100%\"";
					if ($this->issubreport) $w="width=\"100%\"";
				
					$b="1";
					if ($this->issubreport) $b="0";

					if ($this->outputformat==HTML) 
					{
						if ($this->title!='')
						{
							$fecha=date("d/m/Y h:m:s");
							echo "<table $w border=\"0\"><tr><td><h2>$this->title</h2></td><td align=\"right\">$fecha</td></tr></table>";
						}					
						
						echo "<table $w border=\"$b\" cellspacing=\"0\" cellpadding=\"2\">";				
						
					}
					
					while (list($bandname,$bandparams)=each($this->bands))
					{
						$this->renderBand($bandparams);
					}
					if ($this->outputformat==HTML) 
					{
						if ($this->showsummaries)
						{					
							$cs=count($this->phpgrid->printable);
							echo "<tr class=\"reportdetaileven\">";
							echo "<td colspan=\"$cs\" align=\"right\">$this->recordcount registro(s)</td>";							
							echo "</tr>";							
							
						}
						echo "</table>";	
					}
					
					if ($this->outputformat==PDF) 
					{
						/* Close the table (important!) */
						$this->table->endtable();
					}
					
					if ($this->showsummaries)
					{
						if ($this->outputformat==PDF)
						{
							$rm=0;

							// Make an instance of the table class
							switch($this->orientation)
							{
								case LANDSCAPE: 
										{
											$rm=812;
											break;
										}
								case PORTRAIT: 
										{
											$rm=565;
											break;
										}									
							}
						
					
							$this->ypos-=10;					
							
							// Make an instance of the class
							$text=new jh_pdf_flowingtext(&$this->pdf, &$this->ypos, 30, $rm, 30);
	
							$text->setfontsize(9);
							$text->addtext($this->recordcount." registro(s)", true);
							$text->puttext(30,$this->ypos,"right",0);
							
							$this->ypos-=26;
						}
					}
					
					if ($this->outputformat==PDF) 
					{
					
						if (!$this->issubreport)
						{
							/* Close the last page */
							PDF_end_page($this->pdf);

							/* Close the PDF document */
							PDF_close($this->pdf);
	
							/* Output the current part of the in-memory created PDF document */
							print PDF_get_buffer($this->pdf);

							/* Delete the PDFlib object */
							PDF_delete($this->pdf);
						}
					}					
					
				}
				else
				{
					$sbsearch_x=1;
					$this->phpgrid->showformatcombo=TRUE;
					$this->phpgrid->showgrid=0;
					$this->phpgrid->run();
				}
				
			}
			
		
			function renderDetailBand($bandparams)
			{
				if (array_key_exists('grid',$bandparams)) $grid=$bandparams['grid'];
				else $grid=$this->phpgrid;
				
				$params=$this->params;
				$subreports=$bandparams['subreports'];
				
				$field='';
				$value='';
				if (count($params)!=0) 
				{
					reset($params);
					list($field,$value)=each($params);
				}
				
				$fieldvalues=$grid->getrecord($field,$value,$this->filtered);
				
				$o=0;
				while (count($fieldvalues)!=0)
				{
					if ($this->filterfunc!='')
					{
                       $filter=$this->filterfunc;
					   eval ("\$result=$filter(\$fieldvalues);");						
					   if (!$result) 
					   {
						   $fieldvalues=$grid->getrecord($field,$value,$this->filtered);
						   continue;
					   }
					}
					
					if ($o==0) 
					{
						$trc='reportdetailodd';
						$o=1;
					}
					else
					{
						$o=0;
						$trc='reportdetaileven';
					}
					
					if (count($subreports)!=0)
					{
						$trc='reportdetaileven';
						
						if ($this->outputformat==PDF) $this->table->setbgcolors(array(false, false));
						
					}
					
					if ($this->outputformat==HTML) echo "<tr class=\"$trc\">";
					
					
					
					
					reset($grid->printable);
					
					$data=array();
					$i=0;
					while (list($k,$v)=each($grid->printable))
					{
						$val=$fieldvalues[$k];
						
						if ((trim($val)=='') && ($this->outputformat==HTML)) $val='&nbsp;';
						
						if ($this->outputformat==HTML) echo "<td>$val</td>";
						$data[$i]=strip_tags($val);
						$i++;
					}
					if ($this->outputformat==PDF) $this->table->addrow($data);
					if ($this->outputformat==HTML) echo "</tr>";
					
					if (count($subreports)!=0)
					{
						
						reset($subreports);
						$cs=count($grid->printable)-1;
						
						while (list($t,$ps)=each($subreports))
						{
							$subdet=$ps['report'];
							$masterfield=$ps['masterfield'];
							$detailfield=$ps['detailfield'];
							$value=$fieldvalues[$masterfield];
							$subdet->params=array("$detailfield"=>"$value");
							$subdet->outputformat=$this->outputformat;
							$subdet->pdf=$this->pdf;
							$subdet->ypos=$this->ypos;
							reset($this->pdfwidths);
							list($fd,$wd)=each($this->pdfwidths);
							$subdet->subx=$this->subx+$wd;
							$subdet->headercolor=0.5;
							if ($subdet->hasData())
							{
								if ($this->outputformat==HTML) echo "<tr class=\"$trc\">";
								if ($this->outputformat==HTML) echo "<td>&nbsp;</td><td colspan=\"$cs\" align=\"right\">";							
								$subdet->run();
								$this->ypos=$subdet->ypos;
								if ($this->outputformat==HTML) echo "</td>";														
								if ($this->outputformat==HTML) echo "</tr>";							
							}
						}
					}
					
					$fieldvalues=$grid->getrecord($field,$value,$this->filtered);
					
					$this->recordcount++;
					
				}
			}
			
			function renderHeaderBand($bandparams)
			{
				if (array_key_exists('grid',$bandparams)) $grid=$bandparams['grid'];
				else $grid=$this->phpgrid;			
				
				$headerstyle='reportheader';
				if ($this->issubreport) $headerstyle='subreportheader';
				
				$grid->getlookupquery();
				$what=$grid->statustext;
				
				if ($this->outputformat==HTML) 
				{
					if ($what!='')
					{
						$cs=count($grid->printable);
						echo "<tr><td colspan=\"$cs\"><b>$what</b></td></tr>\n";
					}
					echo "<tr class=\"$headerstyle\">";
				}
				
				reset($grid->printable);
					
				while (list($k,$v)=each($grid->printable))
				{
					$column=$grid->getLabel($k);
					if ($this->outputformat==HTML) echo "<td>$column</td>";
					
					if ($this->outputformat==PDF)
					{
						$column=strip_tags($column);
						$colh=new jh_pdf_text_jhpci(&$this->pdf);

						if (array_key_exists($k,$this->coltypes))
						{
							$colr=new jh_pdf_container_jhpci(&$this->pdf);
							
							$barcode=new jh_pdf_barcode_jhpci(&$this->pdf);
							$barcode->settype('128a');

							/* Adjust the height of the barcode */
							$barcode->setheight(20);
							
							
							$bctext=new jh_pdf_text_jhpci(&$this->pdf);
							$bctext->setalignment("center");
							
							$colr->addobject($barcode);
							$colr->addemptyspace(3);
							$bctext->setfontsize($this->pdffontsize);
							$colr->addobject($bctext);
						}						
						else
						{
							$colr=new jh_pdf_text_jhpci(&$this->pdf);	
							$colr->setfontsize($this->pdffontsize);												
						}
						
						$colh->setfontsize($this->pdffontsize);						
						$colh->setfontcolor(1);
								
						if (array_key_exists($k,$this->pdfwidths))
						{
							$w=$this->pdfwidths[$k];
						}
						else $w='*';
						$col=&$this->table->addcolumn($column, $w, $colh, $colr);
					}												
				}
	
				if ($this->outputformat==HTML) echo "</tr>";
			}		
			
			
			function renderBand($bandparams)
			{
				$bandtype=$bandparams['bandtype'];
				switch ($bandtype)
				{
					case DETAIL: 
								{
									$this->renderDetailBand($bandparams);
									break;
								}
											
					case HEADER: 
								{
									$this->renderHeaderBand($bandparams);
									break;
								}								
								
				}			
			}
        }
		

?>