<?php
/*************************************************************************** 
                                                                             
 PHPGrid
                                                                             
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

        /**
        *Data abstraction layer
        */
        include "adodb/adodb.inc.php";


        /**
        *Opens a table with border
        */
        function OpenTable()
        {
                echo "<table bgcolor=\"#000000\" border=\"0\"cellspacing=\"0\" cellpadding=\"1\">";
                echo "<tr>";
                echo "<td>";
                echo "<table bgcolor=\"#FFFFFF\">\n";
                echo "<tr>";
                echo "<td>";
        }

  
		
		/**
        *Closes a table with border
        */
        function CloseTable()
        {
                echo "</td>";
                echo "</tr>";
                echo "</table>";
                echo "</td>";
                echo "</tr>";
                echo "</table>";
        }

        /**
        *PHPGrid - A complete grid solution for PHP
        *
        *This class is represents a grid tied to a database table and shows a navigator to perform
        *the most common management operations.
        *
        *@version 0.1
        *@author Security Data S.A. <info@securitydata.es>
        */
        class PHPGrid
        {
                /**
                * Database host
                * @var string
                */
                var $host;

                /**
                * User to access the database
                * @var string
                */
                var $user;

                /**
                * Password
                * @var string
                */
                var $password;

                /**
                * Database where the table is stored
                * @var string
                */
                var $database;

                /**
                * Table to manage
                * @var string
                */
                var $table;

                /**
                * Edit/Insert action atribute
                * @var string
                */
                var $action='';
				
                /**
                * Database connection
                * @var object
                */
                var $conn='';                                                   //Connection

                /**
                * Last error number
                * @var integer
                */
                var $lasterror;
				
				var $insertarray=array();
				
                /**
                * Whether shows a combo to choose print output format or not
                */				
				var $showformatcombo=FALSE;

                /**
                * Last error message
                * @var string
                */
                var $lasterrmsg;

                /**
                *Whether the grid table is 100% width or not
                */
                var $autosize=false;
				
                /**
                *Name for the edit/insert form
                */
                var $formname='';

                /**
                *Caption for the form and grid
                */
                var $formcaption='';

                 /**
                 *Labels for fields
                 */
                 var $labels=array();

                 /**
                 *Relations for fields
                 */
                 var $relations=array();
				 
                 /**
                 *To link fields with another fields
                 */				 
				 var $linked=array();

                 /**
                 *Controls for fields
                 */
                 var $controls=array();

                 /**
                 *Use colons(:) when showing field labels
                 */
                 var $usecolons=true;

                 /**
                 *A switch to prevent redundant code
                 */
                 var $javascriptinserted=false;

                 /**
                 *Whether shows or net checkboxes
                 */
                 var $showcheckboxes=1;
				 
                 /**
                 *What is the grid showing?
                 */
                 var $units='records';

                 /**
                 *No editable fields
                 */
                 var $noeditable=array();
				 
                 /**
                 *Printable
                 */
                 var $printable=array();				 

                 /**
                 *No insertable fields
                 */
                 var $noinsertable=array();
				 
                 /**
                 *Default values
                 */
                 var $defaultvalues=array();				 

                 /**
                 *Wheter shows or not the statusbar
                 */
                 var $showstatus=1;
				 
                 /**
                 *Wheter shows or not the statustext
                 */				 
				 var $showstatustext=1;
				 
                 /**
                 *Wheter shows or not the grid titles
                 */
                 var $showtitles=1;				 

                 /**
                 *Order to show fields on a form
                 */
                 var $fieldorder=array();

                 /**
                 *Validity checks before submit the form
                 */
                 var $validitychecks=array();

                 /**
                 *Fields don't appear in the grid
                 */
                 var $novisible=array();

                 /**
                 *Calculated fields that extract its information from a function
                 */
                 var $calculatedfields=array();
				 
                 /**
                 *Help for each field
                 */
                 var $fieldhelp='';

                 /**
                 *Array of field value functions
                 */
                 var $getfieldvaluefunctions=array();
				 
				 /**
				 *Auxiliar result set
				 */
				 var $rs;
				 
				 /**
				 *Text to show on the status bar
				 */
				 var $statustext='';
				 
                 /**
                 *Colors for the grid
                 */
                 var $colors=array('#F0F0F0','#E0E0E0','#B6C3F2','#ABCEE9');

                 /**
                 *Ask for confirmation before delete a record
                 */
                 var $confirmdelete=true;
				 
                 /**
                 *Delete message
                 */				 
				 var $deletemessage="¿Está seguro que quiere borrar los registros seleccionados?";

                 /**
                 *Records per page
                 */
                 var $recordsperpage=10;

                 /**
                 *Max field size
                 */
                 var $maxfieldsize=20;

                 /**
                 *Text area default columns
                 */
                 var $defaultcols=30;

                 /**
                 *Text area default rows
                 */
                 var $defaultrows=5;

                 /**
                 *Fields to be included on a form
                 */
                 var $hiddenfields=array();

                 /**
                 *Whether shows the navigator or not
                 */
                 var $shownavigator=true;

                 /**
                 *Allow field sorting
                 */
                 var $allowsort=true;
				 
                 /**
                 *Default order field
                 */				 
				 var $defaultorderfields='';

                 /**
                 *Whether shows the grid or not
                 */
                 var $showgrid=true;

                 /**
                 *This event is fired before an update operation
                 */
                 var $beforeupdate='';
                 /**
                 *This event is fired after an update operation
                 */
                 var $afterupdate='';
                 /**
                 *This event is fired before a delete operation
                 */
                 var $beforedelete='';
                 /**
                 *This event is fired after a delete operation
                 */
                 var $afterdelete='';
                 /**
                 *This event is fired before an insert operation
                 */
                 var $beforeinsert='';
                 /**
                 *This event is fired after an insert operation
                 */
                 var $afterinsert='';

                 /**
                 *Allowed actions
                 */
                 var $allowedactions=array('FIRST'=>'1','PRIOR'=>'1','NEXT'=>'1','LAST'=>'1', 'SEARCH'=>'1',  'HELP'=>'1',
                                           'INSERT'=>'1','DELETE'=>'1','EDIT'=>'1','DETAIL'=>'1');

                 /**
                 *Check if an action is allowed
                 *
                 *This functions search in the {@link $allowedactions} array for $action and returns true if it's 1
                 *
                 *@param string $action Action to check
                 *@return boolean
                 */
                 function allowedto($action)
                 {
                         if ($this->allowedactions[$action]=='1') return (true);
                         else return(false);
                 }


                 /**
                 *Returns the control to edit a field
                 *
                 *This functions performs all the operations to get the right control to edit a field object
                 *
                 *@param object $fieldobject Field object to edit
                 *@param string $fieldname Fieldname to edit
				 *@param object $rs Recordset where the field object belongs
                 *@param string $value Current field value
                 *@return string
                 */
                 function getControl($fieldobject,$fieldname,$rs,$value='')
                 {
                         //By default, the fieldsize is the max_length
						 $fieldsize=$fieldobject->max_length;
						 
                         if (array_key_exists($fieldname,$this->controls))
                         {
                                 $controlarray=$this->controls[$fieldname];
                                 list($controltype,$parameters)=each($controlarray);
								 
								 //If we want to show a detail, there is no control, just the plain value
								 if ($this->action=='detail') $control=$value;
								 else
                                 switch($controltype)
                                 {
                                         case 'edit':
                                         {
                                                 //Edit control
                                                 $control="<input type=\"text\" name=\"$fieldname\" value=\"$value\" ";
                                                 reset($parameters);

                                                 while (list($attrname,$attrvalue)=each($parameters))
                                                 {
                                                         $control.="$attrname=\"$attrvalue\" ";
                                                 }

                                                 $control.=">";
                                                 break;
                                         }
                                         case 'textarea':
                                         {
                                                 //Text area
                                                 $control="<textarea name=\"$fieldname\" ";
                                                 reset($parameters);
                                                 while (list($attrname,$attrvalue)=each($parameters))
                                                 {
                                                         $control.="$attrname=\"$attrvalue\" ";
                                                 }

                                                 $control.=">$value</textarea>";
                                                 break;
                                         }
                                         case 'radio':
                                         {
                                                 //Radio button
                                                 $control="<input type=\"radio\" name=\"$fieldname\" value=\"$value\">";
                                                 break;
                                         }
                                         case 'checkbox':
                                         {
                                                 //Checkbox
                                                 $control="<input type=\"checkbox\" name=\"$fieldname\" value=\"$value\">";
                                                 break;
                                         }
                                         case 'combo':
                                         {
                                                 //Combobox
                                                 $control="<select name=\"$fieldname\">";
                                                 if (array_key_exists('items',$parameters))
                                                 {
                                                         $items=$parameters['items'];
                                                         while (list($val,$caption)=each($items))
                                                         {
                                                                 $control.="<option value=\"$val\">$caption</option>";
                                                         }
                                                 }
                                                 $control.="</select>";

                                                 if ($value!='')
                                                 {
                                                         $control=str_replace("value=\"$value\"","value=\"$value\" selected",$control);
                                                 }
                                                 break;
                                         }
                                         case 'custom':
                                         {
                                                 //If it's custom control, calls a custom function to get the control to show
                                                 if (array_key_exists('function',$parameters))
                                                 {
                                                         $displayfunction=$parameters['function'];
                                                         eval ("\$control=$displayfunction(\$fieldobject,\$value);");
                                                 }
                                                 break;
                                         }
                                         case 'list':
                                         {
												 //Listbox
                                                 $control="<select name=\"$fieldname\" ";
                                                 reset($parameters);
                                                 while (list($attrname,$attrvalue)=each($parameters))
                                                 {
                                                         $control.="$attrname=\"$attrvalue\" ";
                                                 }
                                                 $control.=">";
                                                 if (array_key_exists('items',$parameters))
                                                 {
                                                         for ($i=0;$i<=count($parameters['items'])-1;$i++)
                                                         {
                                                                 $opt=$parameters['items'][$i];
                                                                 $control.="<option value=\"$i\">$opt</option>";
                                                         }
                                                 }
                                                 $control.="</select>";
                                                 if ($value!='')
                                                 {
                                                         $control=str_replace("value=\"$value\"","value=\"$value\" selected",$control);
                                                 }
                                                 break;
                                         }
                                 }
                         }
						 //If no control is specified, then checks the relations array to show a combo or a grid
                         else if (array_key_exists($fieldname,$this->relations))
                         {
                                 $params=$this->relations[$fieldname];
                                 $detailtable=$params['detailtable'];
                                 $linkfield=$params['linkfield'];
                                 $displayfield=$params['displayfield'];
                                 $refpage=$params['refpage'];
                                 $lookup=$params['lookup'];
								 $lookupparams=$params['lookupparams'];
								 $filter=$params['filter'];

								 //Don't know why is this here, but it causes the combos to be set to an incorrect value when adding records 
                                 //if (trim($value)=='') $value=$rs->fields[$fieldobject->name];

                                 $fieldlabel=$this->getLabel($fieldobject->name);
								 
								 if ($this->action!='detail')
								 {
									 //If it has a refpage, then it's a grid, so we get it and add an insert and edit button
									 if ($refpage!='')
									 {
								              $control=$this->getFieldValue($fieldobject,$fieldname,$value);									 
											  $control.="<A HREF=\"$refpage?sbinsert_x=5&amp;$linkfield=$value&amp;where=$linkfield&amp;wherevalue=$value\" title=\"Añadir\"><img src=\"phpgrid/binsert.gif\" border=\"0\"></A>";
											  $control.="<A HREF=\"$refpage?$linkfield=$value&amp;where=$linkfield&amp;wherevalue=$value\" title=\"Editar\"><img src=\"phpgrid/bedit.gif\" border=\"0\"></A>";
											  $control="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\">$control</td></tr></table>";
									 }
									 else
									 {
											 //If it's a 1-N relation, then get a combo of the detail table
											 $control=$this->getTableCombo($fieldobject->name,$detailtable,$linkfield,$displayfield,$value,$filter);
											 if ($lookup!='')
											 {
												 $control.="&nbsp;<a href=\"javascript:show".$fieldobject->name."lookup('$lookup','$linkfield','$displayfield','$lookupparams');\"><img src=\"phpgrid/dots.gif\" width=\"16\" height=\"16\" border=\"0\"></a>";
											 }

											echo "<script language=\"Javascript\">\n";
											echo "function Update".$fieldobject->name."Timer()\n";
											echo "{\n";
											echo "if (getCookie('$linkfield')!='none')\n";
											echo "	{\n";
											echo "  if (getCookie('$linkfield')!='null')\n";
											echo "	  {\n";
											//echo "	    alert(document.cookie);\n";
											echo "      var cb=document.forms[0].$fieldobject->name;\n";
											echo "      cb.options[cb.options.length]=new Option(getCookie('$displayfield'),getCookie('$linkfield'));\n";
											echo "      cb.selectedIndex=cb.options.length-1;\n";											
											echo "        document.cookie='$linkfield=';\n";
											echo "        document.cookie='$displayfield=';\n";												
											echo "	  }\n";											
											echo "	}\n";
											echo "	else\n";
											echo "	{\n";
											echo "	  setTimeout(\"Update".$fieldobject->name."Timer()\", 10);	\n";
											echo "	}\n";
											echo "}\n";
											
											echo "function show".$fieldobject->name."lookup(str_lookup,str_value,str_desc,str_params) \n";
											echo "{\n";
											echo "        document.cookie='$linkfield=none';\n";
											echo "        document.cookie='$displayfield=';\n";											
											echo "        var vLookup = window.open(str_lookup+\"?action=addrecord&linkfield=\"+str_value+\"&displayfield=\"+str_desc, \"lookup\", \"resizable=no,scrollbars=no,status=no,menubar=no,top=200,left=200,\"+str_params);\n";
											echo "        vLookup.opener = self;\n";
											echo "        setTimeout(\"Update".$fieldobject->name."Timer()\", 10);\n";
											echo "\n";
											echo "}\n";
											echo "\n";
											echo "\n";
											echo "</script>\n";

									 }
								 }
								 
                         }
                         else
                         {
								 //If it's a normal field
								 if ($this->action=='detail') $control=$value;
								 else
								 {
									 if ($fieldsize>$this->maxfieldsize) $fieldsize=$this->maxfieldsize;
									 $maxsize=$fieldobject->max_length;

									 //If it's a blob, returns a text area
									 if ($fieldobject->blob)
									 {
											 $control="<textarea name=\"$fieldname\" cols=\"$this->defaultcols\" rows=\"$this->defaultrows\">$value</textarea>";
									 }
									 else
									 {
											 //In any other case
											 $extra='';
											 $valid='';
											 $fname=$this->forname."form";											 
											 
											 //If is a date field, then add it some spice
											 switch($fieldobject->type)
											 {
												case 'date':
												{
													$extra="&nbsp;<a href=\"javascript:show_calendar('document.$fname.$fieldname', document.$fname.$fieldname.value);\"><img src=\"phpgrid/cal.gif\" width=\"16\" height=\"16\" border=\"0\"></a>";
													$valid=" onblur=\"check_date(this)\" ";
												}
											  }
											  
											  //Returns an edit control
											  $control="<input type=\"text\" name=\"$fieldname\" value=\"$value\" size=\"$fieldsize\" maxlength=\"$maxsize\" $valid>";
											  $control.=$extra;

											  //If it's a date field and we are filtering, then add an until field	
											  if (($fieldobject->type=='date') && ($this->action=='lookup'))
											  {
													$fieldname=$fieldname.'_until';  
													$extra="&nbsp;<a href=\"javascript:show_calendar('document.$fname.$fieldname', document.$fname.$fieldname.value);\"><img src=\"phpgrid/cal.gif\" width=\"16\" height=\"16\" border=\"0\"></a>";
													$valid=" onblur=\"check_date(this)\" ";
													
													$control.="&nbsp;-&nbsp;<input type=\"text\" name=\"$fieldname\" value=\"$value\" size=\"$fieldsize\" maxlength=\"$maxsize\" $valid>";
													$control.=$extra;												  
											  }									 
									 }
								 }
                         }
                         return($control);
                 }

                 /**
                 *Returns a combobox with the contents of a table
                 *
                 *This functions creates a combobox with the contents of a table, showing a field and storing as values another field
                 *
                 *@param string $masterfield Name of the combobox to be created
                 *@param string $detailtable Table to show
                 *@param string $lookupfield Field containing the values
                 *@param string $displayfield Field to show on the combo
                 *@param string $selected To set an item as selected
                 *@return string
                 */
                 function getTableCombo($masterfield,$detailtable,$lookupfield,$displayfield,$selected='',$filter='')
                 {
						 $sql = "select * from $detailtable order by $displayfield";
                         $rs=$this->ExecSQL($sql);

                         $control="<select name=\"$masterfield\">";
						 $req=$this->validitychecks[$masterfield]['required'];

						 //If we are in lookup mode, or the field is not required, the first option must be blank	
						 if (($this->action=='lookup') || ($req=='')) 
						 {
							$control.="<option value=\"\"></option>";						 
						 }
						 
						 //Iterate through the table
                         while (!$rs->EOF)
                         {
								 $add=true;
								 
								 //If there is a filter, calls it
								 if ($filter!='')
								 {
									eval ("\$add=$filter(\$rs);");								 
							     }
								 
								 //Add the option to the combo
								 if ($add==true)
								 {
									 $value=$rs->fields[$lookupfield];
									 $display=$rs->fields[$displayfield];
									 $control.="<option value=\"$value\">$display</option>";
								 }

                                 $rs->MoveNext();
                         }
                         $control.="</select>";
						 
						 //Sets a value selected by default
                         $control=str_replace("value=\"$selected\"","value=\"$selected\" selected",$control);
                         return($control);
                 }


                 /**
                 *To get the label of a field
                 *
                 *Returns a label for a field if is set in the labels array, if not returns the field name
                 *
                 *@param string $fname Name of the field
                 *@return string
                 */
                 function getLabel($fname)
                 {
                         if (array_key_exists($fname,$this->labels))
                         {
                                 $fname=$this->labels[$fname];
                         }
                         return($fname);
                 }


                 /**
                 *Performs an update of a specified record in the table
                 */
                 function update()
                 {
						 //Checks the right permissions
                         if (!$this->allowedto('EDIT'))
                         {
                                 echo "No le está permitido realizar esta acción [EDIT]";
                                 exit;
                         }

						 //Calls the event if it exists
                         if ($this->beforeupdate!='')
                         {
                                 $event=$this->beforeupdate;
                                 $sender=$this;
                                 $result=1;
                                 eval ("\$result=$event(\$sender);");
                                 if (!$result) return;
                         }

                         $this->lasterror=0;
                         $this->lasterrmsg='';

						 //Get the table fields
                         $fields=$this->getFields();

                         $filters='';
                         $cond='';
						 
						 
                         for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
								 
								 //If the field is posted from a form
                                 if (array_key_exists($field->name,$_POST))
                                 {
                                         $value=$_POST[$field->name];
										 
										 //And it has a value
										 if (trim($value)!='')
										 {
											 //Reverse the date
											 switch($field->type)
											 {
												 case 'date':
												 {
													 $value=$this->reversedate($value);
													 break;
												 }
											 }
										 }
										 
                                         //If it's the first field
                                         if ($i==0)
                                         {
												 //The condition to find the record
                                                 $cond= "$field->name='$value'";
                                         }
                                         else
                                         {
												//The rest of the fields are values to set 
												if ($filters!='') $filters.=' , ';
												if (trim($value)=='')
												{
													//If the post has no contents, the field must be set to NULL
													$filters.="$field->name=NULL";												
												}
												else
												{
													$filters.="$field->name='$value'";
												}
                                         }
                                 }

                         }

                         //Performs the update
                         $sql = "UPDATE $this->table SET $filters WHERE $cond";
						 
						 
						 
						 $this->ExecSQL($sql);

                         //Calls the event
                         if ($this->afterupdate!='')
                         {
                                 $event=$this->afterupdate;
                                 $sender=$this;
                                 eval ("$event(\$sender);");
                         }
                 }

                 /**
                 *Executes a query
                 *
                 *Executes a query, manage errors and returns a recordset to work on
                 *
                 *@param string $sql SQL to execute
                 *@return object
                 */
                 function ExecSQL($sql)
                 {
                         $rs = $this->conn->Execute($sql);
                         if ($this->conn->ErrorNo()!=0)
                         {
                                 $this->lasterror=3;
                                 $this->lasterrmsg=$this->conn->ErrorMsg();
                         }
                         else
                         {
                                 $this->lasterror=0;
                                 $this->lasterrmsg='';
                         }
                         return($rs);
                 }

                 /**
                 *Executes a query with limit
                 *
                 *Executes a query with limit, manage errors and returns a recordset to work on
                 *
                 *@param string $sql SQL to execute
                 *@param integer $start First record
                 *@param integer $recs How many records
                 *@return object
                 */
                 function SelectLimit($sql,$start,$recs)
                 {
                         $rs = $this->conn->SelectLimit($sql,$start,$recs);
                         if ($this->conn->ErrorNo()!=0)
                         {
                                 $this->lasterror=3;
                                 $this->lasterrmsg=$this->conn->ErrorMsg();
                         }
                         else
                         {
                                 $this->lasterror=0;
                                 $this->lasterrmsg='';
                         }
                         return($rs);
                 }

                 /**
                 *Performs a delete operation
                 */
                 function delete()
                 {
                         //Check permissions
						 if (!$this->allowedto('DELETE'))
                         {
                                 echo "No le está permitido realizar esta acción [DELETE]";
                                 exit;
                         }

                         //Call the event
						 if ($this->beforedelete!='')
                         {
                                 $event=$this->beforedelete;
                                 $sender=$this;
                                 $result=1;
                                 eval ("\$result=$event(\$sender);");
                                 if (!$result) return;
                         }

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         //Get the fields
						 $fields=$this->getFields();

                         $filters='';
                         $cond='';

                         global $currentrecord,$selectedrow;

                         $sql = "select * from $this->table";
                         $sql=$this->filterquery($sql);
                         $field=$fields[0];

                         $values=array();

                         $i=0;
						 
                         //Gets the checked records
						 while (list($k,$v)=each($selectedrow))
                         {
								 $values[$i]=$k;								 
                                 $i++;
                         }

                         //Calls delete record for each record to delete
						 for ($i=0;$i<=count($values)-1;$i++)
                         {
						 
								 $thisvalues=$this->getfieldsvalues($field->name,$values[$i]);
                 						 
                                 //Deletes each record
								 $afields=array($field->name=>$values[$i]);
								 $this->deleterecord($afields);
								 
								 //Call the event
								 if ($this->afterdelete!='')
								 {
									 $event=$this->afterdelete;
									 $sender=$this;
									 eval ("$event(\$sender,\$thisvalues);");
								 }								 
								 

                         }
						 
                         
                 }
				 
                 /**
                 *Returns a detail grid
                 *
                 *Returns a grid to be inserted in a detail form
                 *
                 *@param object $rs Recordset to show
                 *@param boolean $compact Whether compact mode or not
                 *@return string
                 */				
				 function getDetailGrid($rs,$compact)
				 {
					 $this->shownavigator=false;
					 if ($compact) 
					 {
						  $this->colors[0]='#cfd0d3';
						  $this->showstatus=FALSE;		  
						  $this->showtitles=FALSE;
					 }		  
				  
					 $this->showstatustext=FALSE;				  
					 $this->allowedactions=array();
					 $this->allowsort=false;
					 $this->formcaption='';
					 $this->linked=array();
					 $this->showcheckboxes=false;
					 $this->recordsperpage=$rs->RowCount();

					 ob_start();
					 $this->connect();
					 if (!$compact) OpenTable();
					 $this->grid($rs,0,FALSE);
					 if (!$compact) CloseTable();		  
					 $result=ob_get_contents();
					 ob_end_clean();
					 return ($result);
			     }				 

                 /**
                 *Delete a table
                 */
                 function emptytable()
                 {
						 //Check permissions
                         if (!$this->allowedto('DELETE'))
                         {
                                 echo "No le está permitido realizar esta acción [DELETE]";
                                 exit;
                         }


                         $this->connect();

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         //Deletes all the table
                         $sql = "DELETE FROM $this->table";

                         $this->ExecSQL($sql);
                 }

                 /**
                 *Inserts a new record in the table
                 */
                 function insert()
                 {
						 //Check permissions
                         if (!$this->allowedto('INSERT'))
                         {
                                 echo "No le está permitido realizar esta acción [INSERT]";
                                 exit;
                         }

                         //Call the event
                         if ($this->beforeinsert!='')
                         {
                                 $event=$this->beforeinsert;
                                 $sender=$this;
                                 $result=1;
                                 eval ("\$result=$event(\$sender);");
                                 if (!$result) return;
                         }

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $fields=$this->getFields();

                         $fieldnames='';
                         $fieldvalues='';

                         $wherecond='';

                         //Iterates through the table fields
						 for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
								 //If it comes from a post
                                 if (array_key_exists($field->name,$_POST))
                                 {
                                         if ($fieldnames!='') $fieldnames.=', ';
                                         $fieldnames.=$field->name;

                                         if ($fieldvalues!='') $fieldvalues.=', ';
                                         $value=$_POST[$field->name];
										 
										 if (trim($value)!='')
										 {
											 switch($field->type)
											 {
												 case 'date':
												 {
													 $value=$this->reversedate($value);
													 $value=str_replace('/','-',$value);
													 break;
												 }
											 }
										 }
										 
										 //Whether the field has value or not, inserts a NULL or the value
										 if (trim($value)=='') $fieldvalues.='NULL';
                                         else $fieldvalues.="'$value'";

                                         if ($value!='')
										 {
											 if ($wherecond!='') $wherecond.=' and ';
											 $wherecond.=" $field->name='$value' ";
										 }
                                 }
                         }

                         //Performs the insert
						 $sql = "insert into $this->table($fieldnames) values ($fieldvalues)";

                         $this->ExecSQL($sql);

                         $this->insertarray=array();
                         $sql = "select * from $this->table where $wherecond";
						 
                         $rs=$this->ExecSQL($sql);

                         for ($i=0;$i<=($rs->FieldCount())-1;$i++)
                         {
							 $field=$rs->FetchField($i);
                             $this->insertarray[$field->name]=$rs->fields[$field->name];
                         }
						 
                         //Calls the event
						 if ($this->afterinsert!='')
                         {
                                 $event=$this->afterinsert;
                                 $sender=$this;
                                 $insertarray=$this->insertarray;

								 //Calls the event with the newly inserted values
                                 eval ("$event(\$sender,\$insertarray);");
                         }						 

                 }

                 /**
                 *Insert a new record in the table
                 *
                 *This function takes an array(field=>value) and performs an insert into operation
                 *
                 *@param array $fields Field and values array
                 */
                 function insertrecord($fields)
                 {
                         //Check permissions
						 if (!$this->allowedto('INSERT'))
                         {
                                 echo "No le está permitido realizar esta acción [INSERT]";
                                 exit;
                         }

                         $this->connect();
                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $fieldnames='';
                         $fieldvalues='';

                         while (list($field,$value)=each($fields))
                         {
                                 if ($fieldnames!='') $fieldnames.=', ';
                                 $fieldnames.=$field;

                                 if ($fieldvalues!='') $fieldvalues.=', ';
                                 $fieldvalues.="'$value'";
                         }

                         //Performs the insert
						 $sql = "insert into $this->table($fieldnames) values ($fieldvalues)";

                         $this->ExecSQL($sql);

                 }
				 
                 /**
                 *Delete a record in the table
                 *
                 *This function takes an array(field=>value) and performs a delete operation
                 *
                 *@param array $fields Field and values array
                 */				 
                 function deleterecord($fields)
                 {
						 //Check permissions
                         if (!$this->allowedto('DELETE'))
                         {
                                 echo "No le está permitido realizar esta acción [DELETE]";
                                 exit;
                         }

                         $this->connect();
                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $where='';

                         while (list($field,$value)=each($fields))
                         {
                                 if ($where!='') $where.=' and ';
                                 $where.="$field='$value'";

                         }
						 
						 
						 //Get all the records to delete
						 $sql = "select * from $this->table where $where";						 
						 
                         $rs=$this->ExecSQL($sql);

						 $values=array();
						 $i=0;
						 //Iterate through the table
                         while (!$rs->EOF)
                         {
							 $field=$rs->FetchField(0);
							 $value=$rs->fields[$field->name];
							 $values[$i]=$value;	
                             $rs->MoveNext();
							 $i++;
                         }						 
						 
                         //Performs the delete operation
						 reset($values);
						 while (list($k,$v)=each($values))
						 {
							 $sql = "delete from $this->table where $field->name='$v'";
							 $this->ExecSQL($sql);							 
						 }

                         //Now deletes all the detail records on each detail table
						 reset($this->relations);
						 while (list($afname,$params)=each($this->relations))
						 {
							$detailtable=$params['detailtable'];
							$linkfield=$params['linkfield'];
							$refgrid=$params['refgrid'];									 

							if ($refgrid!='')
  							{
								reset($values);
								while (list($k,$value)=each($values))
								{
									$detfields=array($linkfield=>$value);
									$refgrid->deleterecord($detfields);
								}
							}
						 }	

                 }
				 

                 /**
                 *Output a row of the grid
                 *
                 *@param array $row The row contents
                 *@param integer $rownum Number of the row
                 *@param array $rcolors Colors of each cell
                 */
                 function gridRow($row,$rownum,$rcolors,$rs)
                 {
                         global $PHP_SELF;
                         $rowcolor=$this->colors[0];
                         echo "<tr bgcolor=\"$rowcolor\">\n";
                         if ($this->showcheckboxes)
                         {
								  $rownum=$rs->fields[0];						 
                                  echo "<td>";
                                  echo "<input type=\"checkbox\" name=\"selectedrow[$rownum]\">";
                                  echo "</td>\n";
                         }
						 
						 //Iterates through the row
                         for ($i=0;$i<=count($row)-1;$i++)
                         {
                                 $color=$rcolors[$i];
								 
								 //If the user has edit permissions and is the first record
								 if (($this->allowedto('EDIT')) && ($i==0))
								 {
									 global $orderfield;
									 global $ascdesc;
									 $ordst='';
									 if (isset($orderfield))
									 {
										 $ordst="&amp;orderfield=$orderfield";
										 if (isset($ascdesc))
										 {
											 $ordst.="&amp;ascdesc=$ascdesc";
										 }
									 }
									 
									 global $where;
									 global $wherevalue;
									 if (isset($where))
									 {
										 $where=$this->xdecode($where);
										 if (trim($where!='')) $ordst.="&amp;where=$where";
									 }
									 if (isset($wherevalue))
									 {
										 if (trim($wherevalue)!='') $ordst.="&amp;wherevalue=$wherevalue";
									 }									 
						 
									$rr=$rs->fields[0];
									
									//Creates a link to edit that record
								    echo "<td $color><A HREF=\"$PHP_SELF?sbedit_x=1&amp;editrecord=$rr$ordst\" alt=\"Editar registro\" title=\"Editar registro\">$row[$i]</A></td>\n";									
								 }
								 else echo "<td $color>$row[$i]</td>\n";
                         }
                         echo "</tr>\n";
                 }
				 
				 
				 
				 function xdecode($str)
				 {
					$res=rawurldecode($str);
				    $res=str_replace('\\\'','\'',$res);				 
					return($res);
				 }

                 /**
                 *Filters a query
                 *
                 *Here, a query is modified to add more information about order and specific records
                 *
                 *@param string $sql SQL to filter
                 *@return string
                 */
                 function filterquery($sql)
                 {
                         global $where;
                         global $wherevalue;
						 
						 
						 //Adds the where condition
						 $st='';
                         if (isset($where))
                         {
                                 $where=$this->xdecode($where);
                                 if (trim($where!='')) $sql.=" where $where";
							 
                         }
                         if (isset($wherevalue))
                         {
                                 if (trim($wherevalue)!='') 
								 {
									 $sql.="=$wherevalue";
								 }
                         }

                         //Adds the order condition
						 global $orderfield;
                         global $ascdesc;
                         if (isset($orderfield))
                         {
                                 $sql.=" order by $orderfield";
                                 if (isset($ascdesc))
                                 {
                                         if ($ascdesc) $sql.=" asc ";
                                         else $sql.=" desc ";

                                 }
                         }
						 else
						 {
								if (trim($this->defaultorderfields)!='')
								{
									$r=$this->defaultorderfields;
									$sql.=" order by $r ";
								}
						 }
						 
						 //Decode the sql if there is any encoded char
						 $sql=$this->xdecode($sql);
						 $sql=str_replace('\\\'','\'',$sql);
						 
                         return($sql);
                 }

                 /**
                 *Shows the grid
                 *
                 *This function shows the grid based on the operation the user wants to perform
                 *
                 *@param string $operation Operation to perform
                 */
                 function showGrid($operation)
                 {
                         global $currentrecord;
                         global $selectedrow;

                         if (!isset($currentrecord)) $currentrecord=0;

                         
                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $this->connect();


                         //Check permissions
						 if (trim($operation)!='')
                         {
                                  if (!$this->allowedto(strtoupper($operation)))
                                  {
                                         echo "No le está permitido realizar esta acción [$operation]";
                                         exit;
                                  }
                         }
					 

                         switch ($operation)
                         {
                                 case 'next':
                                 {
										 //Advance 1 page
                                         $currentrecord+=$this->recordsperpage;
                                         $sql = "select * from $this->table";
                                         $sql=$this->filterquery($sql);

										 $rs=$this->ExecSQL($sql);

                                         if ($currentrecord>=$rs->RowCount())
                                         {
                                                 $currentrecord=floor($rs->RowCount() / $this->recordsperpage) * $this->recordsperpage;
                                                 if ($currentrecord<0) $currentrecord=0;
                                                 if ($currentrecord>=$rs->RowCount()) $currentrecord=$rs->RowCount()-$this->recordsperpage;
                                         }
                                         break;
                                 }
                                 case 'prior':
                                 {
										 //Return 1 page
                                         $currentrecord-=$this->recordsperpage;
                                         if ($currentrecord<0) $currentrecord=0;
                                         break;
                                 }

                                 case 'first':
                                 {
										 //First record
                                         $currentrecord=0;
                                         break;
                                 }

                                 case 'last':
                                 {
                                         //Last record
										 $sql = "select * from $this->table";
                                         $sql=$this->filterquery($sql);
                                         $rs = $this->ExecSQL($sql);
                                         $currentrecord=floor($rs->RowCount() / $this->recordsperpage) * $this->recordsperpage;
                                         if ($currentrecord>=$rs->RowCount()) $currentrecord=$rs->RowCount()-$this->recordsperpage;
                                         break;
                                 }
                                 case 'insert':
                                 {
                                         //Shows the form to insert a record
										 $this->action='insert';
                                         $this->showForm();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'delete':
                                 {
                                         //Performs the delete
										 $this->delete();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'edit':
                                 {
										 //Show the form to edit a record
										 global $editrecord;
                                         $this->action='update';
										 $this->showForm($editrecord);
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'search':
                                 {
                                         //Show to form to perform a search
										 $this->action='lookup';
                                         $this->showForm();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'detail':
                                 {
                                         //Show the form in detail mode
										 list($k,$v)=each($selectedrow);
                                         $this->action='detail';

                                         $this->showForm($currentrecord+$k);

                                         exit;
                                         break;
                                 }								 
                                 				 
                         }

                         //In any other case, shows the grid
                         $sql = "select * from $this->table";

                         $sql=$this->filterquery($sql);
						 
                         $rs = $this->SelectLimit($sql,$this->recordsperpage,$currentrecord);

                         $this->grid($rs,$currentrecord,TRUE);

                 }

                 /**
                 *Returns the value for a field
                 *
                 *Depending on the field's datatype, it formats the value or calls a custom function to get the value
                 *
                 *@param object $field The field object
                 *@param mixed $value Value for the field
				 *@pram  boolean $compact Ask for a compact field value
                 *@return string
                 */
                 function getFieldValue($field,$fieldname,$value,$compact=FALSE)
                 {
                         //If the field is related to another table
						 if (array_key_exists($fieldname,$this->relations))
                         {
                                 $params=$this->relations[$fieldname];
                                 $tablename=$params['detailtable'];
                                 $linkfield=$params['linkfield'];

                                 $displayfield='';
                                 if (array_key_exists('displayfield',$params))
                                 {
                                         $displayfield=$params['displayfield'];
                                 }
                                 else
                                 {
                                         $displayfunction=$params['displayfunction'];
                                 }

                                 $this->connect();

                                 $sql = "select * from $tablename where $linkfield='$value'";
								 
                                 $rs = $this->ExecSQL($sql);
                                 if ($rs)
                                 {
                                         if ($displayfield!='')
                                         {
                                                 $value=$rs->fields[$displayfield];
                                         }
                                         else
                                         {
                                                 eval ("\$value=$displayfunction(\$rs,\$compact);");
                                         }
                                 }
                         //******************
                         }
                         else
                         if (array_key_exists($field->name,$this->getfieldvaluefunctions))
                         {
                                 $displayfunction=$this->getfieldvaluefunctions[$field->name];
                                 eval ("\$value=$displayfunction(\$field,\$value);");
                         }
                         else
                         {
                                 switch($field->type)
                                 {
                                         case 'timestamp':
                                         {
												 $tim=strtotime($value);
                                                 $value=date('d/m/Y H:m:s',$microtime);
                                                 break;
                                         }
                                         case 'date':
                                         {
												 if (trim($value)!='')
												 {
													 $tim=strtotime($value);
													 $value=date('d/m/Y',$tim);
												 }
                                                 break;
                                         }										 
                                 }
                         }

                         return($value);
                 }


                 /**
                 *Creates a grid from a recordset
                 *
                 *This function perform all is needed to show the grid
                 *
                 *@param object $rs Recordset
                 *@param integer $currentrecord First record of the recordset to show
                 *@param boolean $fullrs If true, no pagination is generated
                 */
                 function grid($rs,$currentrecord=0,$fullrs=TRUE,$filterrs=TRUE)
                 {
                         if (!$this->showgrid) exit;
                         global $PHP_SELF;
						 
						 $fname=$this->forname."grid";
						 
                         $fields=$rs->FieldCount();
						 
						 $fieldobjects=array();
                         for ($i=0;$i<=$fields-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 $fieldobjects[$field->name]=$field;
                         }

                         if (count($this->fieldorder)!=0)
                         {
                                 $thefields=$this->fieldorder;
                         }
                         else
                         {
                                  for ($i=0;$i<=$fields-1;$i++)
                                  {
                                         $field=$rs->FetchField($i);
                                         $thefields[$i]=$field->name;
                                  }
                         }

				 

                         //If there must be a navigator, then, this is a form and must allow navigation
						 if ($this->shownavigator)
                         {
                                 echo "<form name=\"$fname\" method=\"post\" action=\"$PHP_SELF\">\n";
                                 echo "<input type=\"hidden\" name=\"currentrecord\" value=\"$currentrecord\">\n";
                                 global $where,$wherevalue;
                                 if (isset($where))
                                 {
                                         $where=rawurlencode($where);
                                         $wherevalue=rawurlencode($wherevalue);
                                         echo "<input type=\"hidden\" name=\"where\" value=\"$where\">\n";
                                         echo "<input type=\"hidden\" name=\"wherevalue\" value=\"$wherevalue\">\n";
                                 }
								 
                                 global $orderfield;
                                 global $ascdesc;
                                 if (isset($orderfield)) echo "<input type=\"hidden\" name=\"orderfield\" value=\"$orderfield\">\n";
                                 if (isset($ascdesc)) echo "<input type=\"hidden\" name=\"ascdesc\" value=\"$ascdesc\">\n";
                         }


                         $headercolor=$this->colors[1];

                         $colspan=1;
						 reset($thefields);
                         while (list($k,$v)=each($thefields))
                         {
								 $afieldname=$v;
								 $v=$this->getRealFieldName($afieldname);
								 
								 $field=$fieldobjects[$v];
								 
                                 if (!array_key_exists($afieldname,$this->novisible))
                                 {
                                         $colspan++;
                                 }
                         }

                         $colspan+=count($this->calculatedfields);

                         $w='';
                         if ($this->autosize) $w='width="100%"';

                         $border='border="0"';

                         echo "<table $border $w bgcolor=\"#ffffff\">\n";
                         if ($this->formcaption!='')
                         {
                                  echo "<tr bgcolor=\"$headercolor\">\n";
                                  if (!$this->showcheckboxes) $colspan--;
                                 echo "<td colspan=\"$colspan\"><font size=\"+1\"><b>$this->formcaption</b></font></td>";
                                 echo "</tr>\n";
                         }
						 
						 //Shows the title row
						 if ($this->showtitles)
						 {
							 echo "<tr bgcolor=\"$headercolor\">\n";
							 if ($this->showcheckboxes)
							 {
									  echo "<td width=\"10px\">";
									  echo "<input type=\"checkbox\" name=\"checkall\" onClick=\"CheckAllBoxes()\">";
									  echo "</td>\n";
							 }

							 $rcolors=array();
							 reset($thefields);
							 while (list($k,$v)=each($thefields))
							 {						 
								 $afieldname=$v;
								 $v=$this->getRealFieldName($afieldname);
								 
								 $field=$fieldobjects[$v];

                                 $cellcolor='';
                                 if (!array_key_exists($afieldname,$this->novisible))
                                 {

                                         $label=$this->getLabel($afieldname);
                                         $olabel=$label;
                                         global $orderfield, $ascdesc;
                                         $ord='&ascdesc=1';
                                         if (isset($orderfield))
                                         {
                                                 if ($orderfield==$field->name)
                                                 {
                                                         $toadd='&nbsp;(a-z)';
                                                         $ord='&ascdesc=0';
                                                         if (isset($ascdesc))
                                                         {
                                                                 if (!$ascdesc)
                                                                 {
                                                                         $toadd='&nbsp;(z-a)';
                                                                         $ord='&ascdesc=1';
                                                                 }
                                                          }
                                                          $label.=$toadd;
                                                          $cs=$this->colors[2];
                                                          $cellcolor=" bgcolor=\"$cs\"";
                                                 }
                                         }

                                         global $where;
                                         global $wherevalue;
                                         if (isset($where))
                                         {
                                                 $where=$this->xdecode($where);
                                                 $ord.="&where=$where";
                                         }
                                         if (isset($wherevalue))
                                         {
                                                 if (trim($wherevalue)!='') $ord.="&wherevalue=$wherevalue";
                                         }

                                         if ($this->allowsort)
                                         {
                                                  echo "<td $cellcolor><b><A HREF=\"$PHP_SELF?orderfield=$field->name$ord\" title=\"Ordenar por $olabel\">$label</A></b></td>\n";
                                         }
                                         else
                                         {
                                                  echo "<td $cellcolor><b>$label</b></td>\n";
                                         }
                                 }
							 }

							 reset($this->calculatedfields);
							 while (list($fieldname,$displayfunction)=each($this->calculatedfields))
							 {
									 echo "<td><b>$fieldname</b></td>\n";
							 }
							 echo "</tr>\n";
						 }
                         echo "<tr>\n";

						 //Iterates through the records set and output the fields
						 $r=0;
                         while (!$rs->EOF)
                         {
                                 $row=array();
                                 $k=0;
								 
								 reset($thefields);
								 while (list($t,$v)=each($thefields))
								 {						 
									 $afieldname=$v;
									 $v=$this->getRealFieldName($afieldname);
								 
									 $field=$fieldobjects[$v];
								 
                                     if (!array_key_exists($afieldname,$this->novisible))
                                     {
										 $row[$k]=$this->getFieldValue($field,$afieldname,$rs->fields[$field->name],TRUE);
										 
										 if (array_key_exists($afieldname,$this->linked))
										 {
											 $val=$row[$k];
											 $edrec=$rs->fields[$field->name];
											 $page=$this->linked[$afieldname][0]."?sbedit_x=1&editrecord=$edrec";
											 $hint=$this->linked[$afieldname][1];
											 $row[$k]="<A HREF=\"$page\" alt=\"$hint\" title=\"$hint\">$val</A>";
										 }
									 

                                         if (isset($orderfield))
                                         {
											 if ($orderfield==$field->name)
                                             {
												 $cs=$this->colors[2];
                                                 $rcolors[$k]=" bgcolor=\"$cs\"";
                                             }
                                          }
                                          $k++;
                                      }
									 
                                 }
                                 reset($this->calculatedfields);
                                 while (list($fieldname,$displayfunction)=each($this->calculatedfields))
                                 {
                                         eval ("\$value=$displayfunction(\$rs);");
                                         $row[$k]=$value;
                                         $k++;
                                  }

                                 $this->gridRow($row,$r,$rcolors,$rs);

                                 $rs->MoveNext();
                                 $r++;
                         }
                         echo "</tr>\n";

                         //If must show the status
						 if ($this->showstatus)
                         {
                                  echo "<tr bgcolor=\"$headercolor\">\n";

                                 
								 if ($filterrs)
								 {
								    $sql = "select * from $this->table";
									$sql=$this->filterquery($sql);	 
									if ($fullrs) $rs = $this->ExecSQL($sql);									
								 }
								 else
								 {
								    $sql=$this->getlookupquery();	 
									$rs = $this->ExecSQL($sql);																	 
								 }	
                                 

                                 $totalrecords=$rs->RowCount();
                                 $currentrecord++;

								 if ($totalrecords==0) $currentrecord=0;
                                  $recs="$currentrecord de $totalrecords";
                                 if (!$fullrs)
                                 {
                                         $recs="$totalrecords $this->units";
                                  }

                                 if (!$this->showcheckboxes) $colspan--;
                                 echo "<td align=\"right\" colspan=\"$colspan\">$recs</td>\n";
                                  echo "</tr>\n";
                         }


                         //If must show the navigator
						 if ($this->shownavigator)
                         {
                                 echo "<tr bgcolor=\"$headercolor\">\n";

                                 if (!$this->showcheckboxes) $colspan--;
                                 echo "<td align=\"right\" colspan=\"$colspan\">\n";
                                 include "phpgrid/navigator.php";
                                 echo "</td>\n";
                                 echo "</tr>\n";
                         }
                         
						 //If must show the status text
						 if ($this->showstatustext)
                         {
                                  echo "<tr bgcolor=\"$headercolor\">\n";
								  $this->updatestatustext($rs);
								  
								 global $stext;
								 if ($stext!='')
								 {
									 $stext=urldecode($stext);
									 $this->statustext=$stext;
								 }
						 
								  $stat=$this->statustext;

                                 if (!$this->showcheckboxes) $colspan--;
                                 echo "<td align=\"right\" colspan=\"$colspan\">$stat</td>\n";
                                  echo "</tr>\n";
                         }						 
                         echo "</table>\n";
						 
						 //Closes the form if needed
                         if ($this->shownavigator)
                         {
                                 global $stext;
                                 if (isset($stext))
                                 {
                                         $stext=rawurlencode($stext);
                                         echo "<input type=\"hidden\" name=\"stext\" value=\"$stext\">\n";
                                 }				
								 echo "</form>\n";
                         }
                 }
				 
                 /**
                 *Updates the status text based on the current query
                 *
                 *This function checks all the filter parameters to show a 'readable' string
                 *
                 */				 
                 function updatestatustext($rs)
                 {
                         global $where;
                         global $wherevalue;
						 
						 $st='';
                         if (isset($where))
                         {
                                 $where=$this->xdecode($where);
                                 if (trim($where!='')) $sql.=" where $where";
								 if (isset($wherevalue))
								 {
										 if (trim($wherevalue)!='') 
										 {
											 $sql.="=$wherevalue";
											 
											 for ($i=0;$i<=($rs->FieldCount())-1;$i++)
											 {
												 $fieldobject=$rs->FetchField($i);
												 if (strtolower($fieldobject->name)==strtolower($where))
												 {
													 break;
												 }
											 }
											 $label=strtolower($this->getLabel($fieldobject->name));
											 $value=$this->getFieldValue($fieldobject,$fieldobject->name,$wherevalue);
											 $st.="en los que $label sea $value ";
										 }
								 }
						 }

                         global $orderfield;
                         global $ascdesc;
                         if (isset($orderfield))
                         {
                                 $sql.=" order by $orderfield";
								
								 $label=strtolower($this->getLabel($orderfield));
								 $st.="ordenados por $label ";
											 
                                 if (isset($ascdesc))
                                 {
                                         if ($ascdesc) 
										 {
											 $sql.=" asc ";
											 $st.=" ascendentemente ";
										 }
                                         else 
										 {
											 $sql.=" desc ";
											 $st.=" descendentemente ";											 
										 }

                                 }
                         }
						 else
						 {
								if (trim($this->defaultorderfields)!='')
								{
									$r=$this->defaultorderfields;
									$sql.=" order by $r ";
								}
						 }
						 
						 if (trim($st)!='') 
						 {
							 $this->statustext="Mostrando los registros $st ";

						 }

                         return($sql);
                 }				 

                 /**
                 *Connect to the database
                 *
                 *This function connects to the database and stores the connection on {@link $conn}
                 *
                 */
                 function connect()
                 {

                         if (empty($this->conn))
                         {
                                 $this->lasterror=0;
                                 $this->lasterrmsg='';

                                 $this->conn = ADONewConnection('mysql');
                                 $this->conn->Connect($this->host,$this->user,$this->password,$this->database);
                         }

                         if ($this->conn->ErrorNo()!=0)
                         {
                                 $this->lasterror=3;
                                 $this->lasterrmsg=$this->conn->ErrorMsg();
                         }
                 }

                 /**
                 *Gets all the field objects for the table
                 *
                 *This returns an array containing all the field objects for the table
                 *
                 *@return array
                 */

                 function getFields()
                 {
                         $fields=array();

                         $this->connect();

                         $sql = "select * from $this->table";

                         $rs = $this->ExecSQL($sql);

                         for ($i=0;$i<=($rs->FieldCount())-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 $fields[$i]=$field;
                         }

                         return($fields);
                 }


                 /**
                 *Initialize validity checks
                 *
                 *This function fills the {@link $validitychecks} array with the information of the table
                 *
                 */
                 function initValidityChecks()
                 {
                         $this->connect();

                         $sql = "select * from $this->table";

                         $rs = $this->ExecSQL($sql);

                         for ($i=0;$i<=($rs->FieldCount())-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 if ($field->not_null)
                                 {
                                         $this->validitychecks[$field->name]=array('required'=>'1');
                                 }
                         }
                 }

                 /**
                 *Filters a record set
                 *
                 *This function get field values from the $_POST array and filters the table by those values
                 *
                 */
                 function lookup()
                 {
						 global $currentrecord;
						 $sql=$this->getlookupquery();

						 $currentrecord=0;
						 $rs = $this->SelectLimit($sql,$this->recordsperpage,$currentrecord);

                         $this->grid($rs,0,TRUE,FALSE);
                 }
				 
				 function reversedate($value)
				 {
					 $pieces=explode('/',$value,3);
					 if (count($pieces)!=3) $pieces=explode('-',$value,3);
					 $value="$pieces[2]/$pieces[1]/$pieces[0]";
					 return($value);
				 }
				 
                 /**
                 *Returns a filtered query
                 *
                 *This function get field values from the $_POST array and filters the table by those values
                 *
                 */
                 function getlookupquery()
                 {
                         global $where;

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $fields=$this->getFields();

                         $filters='';
						 $st='';

                         //Iterates through all the table fields
						 for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
								 
								 $value='';
								 $value_set=FALSE;
								 
								 $value_until='';
								 $until_set=FALSE;								 								 
								 
								 //If the field is on the post array
                                 if (array_key_exists($field->name,$_POST))
                                 {
                                      $value=trim($_POST[$field->name]);
									  $value_set=TRUE;
								 }
								 
								 //If there is an until value
                                 if (array_key_exists($field->name."_until",$_POST))
                                 {
                                      $value_until=trim($_POST[$field->name."_until"]);
									  $until_set=TRUE;
								 }								 
								 
								 //If there is something to filter
                                 if (($value!='') || ($value_until!=''))
                                 {
												 //If it's a date, then, reverse the values
												 switch($field->type)
												 {
													 case 'date':
													 {
														 if ($value!='')
														 {
															 $value=$this->reversedate($value);
														 }
														 if ($value_until!='')
														 {
															 $value_until=$this->reversedate($value_until);
														 }														 
														 break;
													 }
												 }
												 
												 //If there is an until value
												 if ($until_set)
												 {
														 $fname=strtolower($this->getLabel($field->name));
														 if ($value!='')
														 {
															 if ($filters!='') $filters.=' AND ';
															 if ($st!='') $st.=' y ';													 
															  $filters.="$field->name >= '$value'";													 
															  
                                                              $vval=$this->getFieldValue($field,$field->name,$value);

															  $st.="<b>$fname</b> sea mayor o igual que '$vval'";
														  }
														  
														 if ($value_until!='')
														 {
															 if ($filters!='') $filters.=' AND ';
															 if ($st!='') $st.=' y ';													 
															  $filters.="$field->name <= '$value_until'";	
															  
															  $vval=$this->getFieldValue($field,$field->name,$value_until);

                                                              if ($value=='') $st.="<b>$fname</b> sea ";
														  
															  $st.="menor o igual que '$vval'";
														  }														  
												 }
												 else
												 {
													 if ($filters!='') $filters.=' AND ';
													 if ($st!='') $st.=' y ';

													 $amp=strpos($value,'%');
												 
													 //If it's a relation field, or not includes an %, then it's an equal relation
													 if ((array_key_exists($field->name,$this->relations)) || ($amp===false))
													 {	
															  $filters.="$field->name = '$value'";
															  $fname=strtolower($this->getLabel($field->name));
															  
															  $vval=$this->getFieldValue($field,$field->name,$value);
															  
															  $st.="$fname sea '$vval'";
													 }
													 else
													 {
															  $filters.="$field->name LIKE '$value'";
															  $fname=strtolower($this->getLabel($field->name));
															  $avalue=str_replace('%','',$value);
															  
															  $aval=$this->getFieldValue($field,$field->name,$avalue);
															  
															  //We set the status depending on the position of the %
															  if (($value[strlen($value)-1]=='%') && ($value[0]=='%'))
															  {
																  $st.="$fname contenga '$avalue'";
															  }
															  else if ($value[strlen($value)-1]=='%')
															  {
																  $st.="$fname comience por '$avalue'";
															  }
															  else if ($value[0]=='%')
															  {
																  $st.="$fname termine en '$avalue'";
															  }														  
															  else
															  {
																  $st.="$fname sea '$avalue'";
															  }
													 }
												 }
                                  }
                         }
						 
						 
						 //Set the status text if needed
						 if (trim($st)!='')
						 {
							 $st="Mostrando los registros en los que $st";
							 $this->statustext=$st;
							 
							 global $stext;
							 $stext=$this->statustext;
						 }
					 


					     //Creates the SQL string 
						 $sql = "select * from $this->table";
						 
						 if (trim($filters)!='')  
						 {
							 $sql.=" where $filters";
						 }
					
					     $where=$filters;						 
						 
                         global $orderfield;
                         global $ascdesc;
						 
                         if (isset($orderfield))
                         {
                                 $sql.=" order by $orderfield";
                                 if (isset($ascdesc))
                                 {
                                         if ($ascdesc) $sql.=" asc ";
                                         else $sql.=" desc ";

                                 }
                         }
						 else if ($this->defaultorderfields!='')
						 {
                                 $sql.=" order by $this->defaultorderfields";						 
						 }
						 
						 return($sql);
                 }				 
				 
                 /**
                 *Returns the real field name
                 *
                 *@param string $v Field name to strip
                 */				 
				 function getRealFieldName($v)
				 {
					 //A fieldname can be a relation and can include <n>
					 $result=$v;
					 $p=strpos($result,'<');
					 if ($p)
					 {
					   $result=substr($result,0,$p);     
					 }					 
					 return($result);
				 }

                 /**
                 *Shows the edit/insert form
                 *
                 *This function shows a form to edit/insert a record
                 *
                 *@param integer $record if specified, allows to edit the record contents
                 */
                 function showForm($record=-1)
                 {
                         global $PHP_SELF;

                         $this->insertJavaScript();

                         $showvalues=($record!=-1);


                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $this->connect();
							 
					     $sql = "select * from $this->table";						 
						 if ($showvalues)
						 {
					        $rs = $this->ExecSQL($sql);						 
							$f=$rs->FetchField(0);
							$ffield=$f->name;
							
							$sql.=" where $ffield='$record'";
						
						 }
						 else
						 {
							 $sql=$this->filterquery($sql);						 
						 }
						 
					     $rs = $this->ExecSQL($sql);

                         $fields=$rs->FieldCount();

                         OpenTable();
                         $onsubmit='';
						 
						 //If the action to perform is different than lookup and detail, then we setup the validity checks
                         if (($this->action!='lookup') && ($this->action!='detail'))
                         {
                                 if (count($this->validitychecks)>=1)
                                 {
                                         $checks='';
                                         reset($this->validitychecks);
                                         while (list($k,$v)=each($this->validitychecks))
                                         {

                                                 if ($checks!='')
                                                 {
                                                         $checks.=',';
                                                 }
                                                 $flabel=$k;
                                                 if (array_key_exists($k,$this->labels))
                                                 {
                                                       $flabel=$this->labels[$k];
                                                 }

                                                 $checks.="'$k','$flabel','";
                                                 if ($v['required']) $checks.="R";
                                                 if ($v['numeric']) $checks.="isNum";
                                                 if ($v['range']!='') $checks.="$v[range]";
                                                 if ($v['email']) $checks.="isEmail";
                                                 $checks.="'";
                                          }
                                         $onsubmit="onSubmit=\"MM_validateForm($checks);return document.MM_returnValue\"";
                                  }
                         }
						 
						 $fname=$this->forname."form";
						 
                         echo "<form name=\"$fname\" method=\"post\" action=\"$PHP_SELF\" $onsubmit>\n";

                         //Dump the hidden fields we need
						 echo "<input type=\"hidden\" name=\"form_action\" value=\"$this->action\">\n";
                         global $where,$wherevalue;
                         if (isset($where))
                         {
                                 $where=rawurlencode($where);
                                 $wherevalue=rawurlencode($wherevalue);
                                 echo "<input type=\"hidden\" name=\"where\" value=\"$where\">\n";
                                 echo "<input type=\"hidden\" name=\"wherevalue\" value=\"$wherevalue\">\n";
                         }

                         global $orderfield;
                         global $ascdesc;
                         if (isset($orderfield)) echo "<input type=\"hidden\" name=\"orderfield\" value=\"$orderfield\">\n";
                         if (isset($ascdesc)) echo "<input type=\"hidden\" name=\"ascdesc\" value=\"$ascdesc\">\n";

                         echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" >\n";

                         $headercolor=$this->colors[1];

                         
						 //Sets the form caption
						 if ($this->formcaption!='')
                         {
                                 $caption=$this->formcaption;
                                 switch($this->action)
                                 {
                                         case 'insert': $caption.=' - Añadir nuevo';
                                                        break;
                                         case 'update': $caption.=' - Modificar';
                                                        break;
                                         case 'lookup': $caption.=' - Filtrar';
                                                        break;
                                         case 'detail': $caption.=' - Detalle';
                                                        break;														

                                 }

                                 echo "<tr><td colspan=\"2\" bgcolor=\"$headercolor\"><b>$caption</b></td></tr>\n";
                                 echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
                         }

                         $fieldobjects=array();
                         for ($i=0;$i<=$fields-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 $fieldobjects[$field->name]=$field;
                         }

                         if (count($this->fieldorder)!=0)
                         {
                                 $thefields=$this->fieldorder;
                         }
                         else
                         {
                                  for ($i=0;$i<=$fields-1;$i++)
                                  {
                                         $field=$rs->FetchField($i);
                                         $thefields[$i]=$field->name;
                                  }
                         }

                         //Iterates through the fields to show
						 while (list($k,$v)=each($thefields))
                         {
								 $afieldname=$v;
								 $v=$this->getRealFieldName($afieldname);
								 
								 $field=$fieldobjects[$v];

                                 $fname=$field->name;
								 
								 //If we are in edit mode
                                 if ($showvalues)        
                                 {
                                         //If it's editable
										 if (!array_key_exists($fname,$this->noeditable))
                                         {
                                                 $value=$rs->fields[$field->name];
												 
												 //We convert the value
												 if (trim($value)!='')
												 {
													 switch($field->type)
													 {	
														 case 'timestamp':
														 {
															 $tim=strtotime($value);
															 $value=date('d/m/Y H:m:s',$microtime);
															 break;
														 }
														 case 'date':
														 {
															 $tim=strtotime($value);
															 $value=date('d/m/Y',$tim);
															 break;
														 }										 
													 }
												 }
								 
                                                 //Get the control
												 $control=$this->getControl($field,$afieldname,$rs,$value);
                                                 $label=$this->getLabel($afieldname);
                                                 if ($this->usecolons) $label=$label.':';

                                                 echo "<tr>\n";
                                                 echo "<td valign=\"top\">";
												 if ($this->action=='detail') echo "<b>$label</b>";
												 else echo "$label";

                                                 //Setup required fields
                                                 if (($this->action!='lookup') && ($this->action!='detail'))
                                                 {
                                                         if (array_key_exists($fname,$this->validitychecks))
                                                         {
                                                                 if ($this->validitychecks[$fname]['required']) echo "(*)";
                                                         }
                                                 }
                                                 echo "</td><td>$control</td>\n";
                                                 echo "</tr>\n";
												 
                                         }
										 
										 //Setup hidden fields under this conditions
										 if ((array_key_exists($fname,$this->novisible)) && (array_key_exists($fname,$this->noinsertable)))
										 {
													 $value=$rs->fields[$field->name];
													 echo "<input type=\"hidden\" name=\"$fname\" value=\"$value\">\n";
										 }																						 

                                 }
                                 else
                                 {
                                         //Try to get the value for the field from Globals or from default values
										 $value='';
                                         if (array_key_exists($field->name,$GLOBALS))
                                         {
                                                 $value=$GLOBALS[$field->name];
                                         }
										 else if (array_key_exists($field->name,$this->defaultvalues)) 
										 {
											 if ($this->action!='lookup') $value=$this->defaultvalues[$field->name];
										 }
										 
                                         //If it's insertable
										 if (!array_key_exists($fname,$this->noinsertable))
                                         {
                                                 //Don't know why this is here
												 if ($this->action=='lookup')
                                                 {
                                                         //if (array_key_exists($fname,$this->relations))
                                                         //{
                                                         //        continue;
                                                         //}
                                                 }

												 $control=$this->getControl($field,$afieldname,$rs,$value);

												 //If we are inserting
												 if ($this->action=='insert')
												 {
													 //If it's a relationfined
													 if (array_key_exists($afieldname,$this->relations))
													 {
															 //If there is no control or no display function, then skip this field
															 if ($control=='') continue;
															 if ($this->relations[$afieldname]['displayfunction']!='') continue;
													 }
												 }

												 $label=$this->getLabel($afieldname);
												 if ($this->usecolons) $label=$label.':';

												 echo "<tr>\n";
												 echo "<td>$label";
												 if ($this->action!='lookup')
												 {
													 if (array_key_exists($fname,$this->validitychecks))
													 {
															 if ($this->validitychecks[$fname]['required']) echo "(*)";
													 }
												 }
											 echo "</td><td>$control</td>\n";
											 echo "</tr>\n";
										 }
								 }
						 }
		
                         //Buttons
						 $submit="<INPUT TYPE=\"Submit\" VALUE=\"Aceptar\">";
                         $reset="<INPUT TYPE=\"Reset\" VALUE=\"Cancelar\">";

					     echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";

						 //In all actions except detail, print the buttons, required reminder and format combo
						 if ($this->action!='detail')
						 {
							 echo "<tr bgcolor=\"$headercolor\"><td>&nbsp;</td><td align=\"right\">$submit&nbsp;$reset</td>\n";
							 $required='';
							 if ($this->action!='lookup')
							 {
									 $required='(*) Campo requerido';
									 echo "<tr bgcolor=\"$headercolor\"><td>&nbsp;</td><td align=\"right\">$required</td>\n";
							 }
							 else
							 {
								 if ($this->showformatcombo)
								 {
									 $combo ="<select name=\"output_format\">\n";
									 $combo.="<option value=\"1\">HTML</option>\n";
									 $combo.="<option value=\"2\">PDF</option>\n";								 
									 $combo.="</select>";

									 echo "<tr><td>Formato de salida</td><td align=\"right\">$combo</td>\n";
								 }
							 }
							 
						 }
                         echo "</table>\n";
						 
						 //Output hidden fields
                         reset($this->hiddenfields);
                         while (list($f,$v)=each($this->hiddenfields))
                         {
                                 echo "<input type=\"hidden\" name=\"$f\" value=\"$v\">\n";
                         }
                         echo "</form>\n";

                         CloseTable();
                         echo "<br>\n";
                 }

                 /**
                 *Class constructor
                 *
                 *Creates the object an stores the connection information
                 *
                 *@param string $host Database host
                 *@param string $user Database user
                 *@param string $password Database password
                 *@param string $database Database to connect
                 *@param string $table Table to manage
                 */
                 function PHPGrid($host,$user,$password,$database,$table)
                 {
                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $this->host=$host;
                         $this->user=$user;
                         $this->password=$password;
                         $this->database=$database;
                         $this->table=$table;

                         $this->initValidityChecks();
                 }


                 /**
                 *Checks if a record exists
                 *
                 *This function is useful to know if a record exists in the table
                 *
                 *@param string $field Field name
                 *@param string $value Value of the field
                 *@return boolean
                 */
                 function recordexists($field,$value)
                 {
                         $sql = "select * from $this->table where $field=$value";
                         $this->connect();
                         $rs = $this->ExecSQL($sql);
                         if ($rs)
                         {
                                 if ($rs->RowCount()!=0) return(1);
                                 else return(0);
                         }
                         else return(0);

                 }

                 /**
                 *Get field values
                 *
                 *Selects a record and returns an array with field=>value
                 *
                 *@param string $field Field name
                 *@param string $value Value of the field
                 *@return array
                 */
                 function getfieldsvalues($field,$value,$relations=FALSE)
                 {
						 if ($this->rs)
						 {
							 $this->rs->MoveNext();
							 if ($this->rs->EOF) 
							 {
								 return(array());
							 }
						 }
						 else
						 {
							 $sql = "select * from $this->table where $field=$value";
							 $this->connect();
							 $this->rs= $this->ExecSQL($sql);
						 }
						
						 $fields=array();

                         for ($i=0;$i<=($this->rs->FieldCount())-1;$i++)
                         {
                                 $field=$this->rs->FetchField($i);
								 $value=$this->rs->fields[$field->name];
								 if ($relations==TRUE) $value=$this->getFieldValue($field,$field->name,$value);
                                 $fields[$field->name]=$value;
                         }
                         return($fields);
                 }
				 
                 
                 /**
                 *Get a record
                 *
                 *Selects a record and returns an array with field=>value
                 *
                 *@param string $field Field name
                 *@param string $value Value of the field
				 *@param boolean $filtered Whether it filters the recordset or not
                 *@return array
                 */				 
				 function getrecord($field,$value,$filtered=FALSE)
                 {
						 if ($this->rs)
						 {
							 $this->rs->MoveNext();
							 if ($this->rs->EOF) 
							 {
								 return(array());
							 }
						 }
						 else
						 {
							 if ($filtered) 
							 {
								 $sql=$this->getlookupquery();
							 }
							 else
							 {
								 $sql = "select * from $this->table";
								 if ($field!='') $sql.=" where $field=$value";
							 }
							 $this->connect();
							 $this->rs= $this->ExecSQL($sql);
							 
							 if ($this->rs->EOF) 
							 {
								 return(array());
							 }							 
						 }
						
						 $fields=array();

                         for ($i=0;$i<=($this->rs->FieldCount())-1;$i++)
                         {
                                 $field=$this->rs->FetchField($i);
								 $value=$this->rs->fields[$field->name];
								 $value=$this->getFieldValue($field,$field->name,$value);
                                 $fields[$field->name]=$value;
                         }
                         return($fields);
                 }				 
				 
                 /**
                 *Insert required JavaScript code
                 *
                 *To perform some client operations in the user browser, this Javascript code is needed, it sets a switch to prevent double insertions
                 *
                 */
                 function insertJavaScript()
                 {
					 if (!$this->javascriptinserted)
					 {
                         $this->javascriptinserted=true;
?>
<SCRIPT LANGUAGE="JavaScript" SRC="phpgrid/ts_picker.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="phpgrid/lookup.js"></SCRIPT>
<script language="JavaScript">
<!--
function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_changeProp(objName,x,theProp,theValue) { //v3.0
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)) eval("obj."+theProp+"="+theValue+"");
}

function MM_getchecked(objName,x) { //v3.0
  var obj = MM_findObj(objName);
  if (obj) return(obj.checked);
}

function MM_validateForm() { //v4.0

  var i,p,q,nm,ms,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { ms=args[i+1]; nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+ms+' debe contener una dirección de e-mail.\n';
      } else if (test!='R') {
        if (isNaN(val)) errors+='- '+ms+' debe contener un número.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (val<min || max<val) errors+='- '+ms+' debe contener un número entre '+min+' y '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+ms+' es obligatorio.\n'; }
  } if (errors) alert('Este formulario tiene los siguientes errores:\n'+errors);
  document.MM_returnValue = (errors == '');
}

function CheckAllBoxes()
{
        var value=MM_getchecked('checkall','');
        var toset='';
        if (value) toset='true';
        else toset='false';


        for (i=0;i<=9;i++)
        {
                MM_changeProp('selectedrow['+i+']','','checked',toset,'INPUT/CHECKBOX');
        }
}
function confirmDelete()
{
         if (confirm("<?php echo $this->deletemessage; ?>"))
         {
                 return(true);
         }
         else
         {
                 return(false);
         }
}

function showHelpWindow()
{
  var l=(screen.width-640) / 2; 
  var t=(screen.height-480) / 2;   

  window.open('<?php echo $PHP_SELF ?>?action=help', 'help','resizable=yes,scrollbars=yes,status=no,menubar=no,top='+t+',left='+l+',width=640,height=480');
}

//-->
</script>
<SCRIPT LANGUAGE="JavaScript">

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->
<!-- Original:  Torsten Frey (tf@tfrey.de) -->
<!-- Web Site:  http://www.tfrey.de -->

<!-- Begin
function check_date(field){
var checkstr = "0123456789";
var DateField = field;
var Datevalue = "";
var DateTemp = "";
var seperator = "/";
var day;
var month;
var year;
var leap = 0;
var err = 0;
var i;
   err = 0;
   DateValue = DateField.value;
   /* Delete all chars except 0..9 */
   for (i = 0; i < DateValue.length; i++) {
	  if (checkstr.indexOf(DateValue.substr(i,1)) >= 0) {
	     DateTemp = DateTemp + DateValue.substr(i,1);
	  }
   }
   DateValue = DateTemp;
   /* Always change date to 8 digits - string*/
   /* if year is entered as 2-digit / always assume 20xx */
   if (DateValue.length == 6) {
      DateValue = DateValue.substr(0,4) + '20' + DateValue.substr(4,2); }
   if (DateValue.length != 8) {
      err = 19;}
   /* year is wrong if year = 0000 */
   year = DateValue.substr(4,4);
   if (year == 0) {
      err = 20;
   }
   /* Validation of month*/
   month = DateValue.substr(2,2);
   if ((month < 1) || (month > 12)) {
      err = 21;
   }
   /* Validation of day*/
   day = DateValue.substr(0,2);
   if (day < 1) {
     err = 22;
   }
   /* Validation leap-year / february / day */
   if ((year % 4 == 0) || (year % 100 == 0) || (year % 400 == 0)) {
      leap = 1;
   }
   if ((month == 2) && (leap == 1) && (day > 29)) {
      err = 23;
   }
   if ((month == 2) && (leap != 1) && (day > 28)) {
      err = 24;
   }
   /* Validation of other months */
   if ((day > 31) && ((month == "01") || (month == "03") || (month == "05") || (month == "07") || (month == "08") || (month == "10") || (month == "12"))) {
      err = 25;
   }
   if ((day > 30) && ((month == "04") || (month == "06") || (month == "09") || (month == "11"))) {
      err = 26;
   }
   /* if 00 ist entered, no error, deleting the entry */
   if ((day == 0) && (month == 0) && (year == 00)) {
      err = 0; day = ""; month = ""; year = ""; seperator = "";
   }
   /* if no error, write the completed date to Input-Field (e.g. 13.12.2001) */
   if (err == 0) {
      DateField.value = day + seperator + month + seperator + year;
   }
   /* Error-message if err != 0 */
   else {
      alert("¡La fecha es incorrecta! Debe estar rellenada con ceros, por ejemplo 1/12/1975 es 01/12/1975 y debe ser válida.");
      DateField.select();
	  DateField.focus();
   }
}
//  End -->
</script>
<?php
					}
                 }

                 /**
                 *Edits or inserts a record
                 *
                 *This function checks if a record exists, if exists performs an edit operation and if not performs an insert operation
                 *
                 *@param string $field Field name
                 *@param string $value Value of the field
                 */
                 function editorinsert($field,$value)
                 {
                         $sql = "select * from $this->table where $field=$value";
                         $this->connect();

                         $rs = $this->ExecSQL($sql);
                         if ($rs)
                         {
                                 if ($rs->RowCount()==0)
                                 {
                                         $operation='insert';
                                         $this->hiddenfields=array($field=>$value);
                                 }
                                 else
                                 {
                                         //This code is shit, I will fix it when I have more time
                                         $sql = "select * from $this->table";
                                         $this->connect();

                                         $rs = $this->ExecSQL($sql);

                                         global $selectedrow,$currentrecord;

                                         $currentrecord=0;

                                         while (!$rs->EOF)
                                         {
                                                 $fvalue=$rs->fields[$field];
                                                 if ($fvalue==$value)
                                                 {
                                                         break;
                                                 }
                                                 $rs->MoveNext();
                                                 $currentrecord++;
                                          }
                                          $selectedrow=array('0'=>'on');
                                          $operation='edit';
                                 }
                                 $this->showGrid($operation);
                         }

                 }
				 
                 /**
                 *Updates a record
                 *
                 *This function updates a record with the set sentence
                 *
                 *@param string $field Field name
                 *@param string $value Value of the field
                 *@param string $set Fields and Values to set
                 */
                 function updaterecord($field,$value,$set)
                 {
					     
                         $sql = "update $this->table set $set where $field=$value";
                         $this->connect();
						 
                         $rs = $this->ExecSQL($sql);
                 }				 

                 /**
                 *Main method
                 *
                 *This function manages all the operations for the grid, in normal conditions you don't need to call another method
                 *
                 */
                 function run()
                 {
         
						 global $action;
                         global $form_action;
                         global $sbfirst_x;
                         global $sbprior_x;
                         global $sbnext_x;
                         global $sblast_x;
                         global $sbinsert_x;
                         global $sbdelete_x;
                         global $sbedit_x;
                         global $sbsearch_x;
                         global $sbcancel_x;
                         global $sbrefresh_x;
                         global $sbdetail_x;						 
						 
						 if (!isset($action))
						 {
							 $this->statustext='Mostrando todos los registros';
                         
							 $this->insertJavaScript();
						 
							 $operation='';
							 if (isset($sbfirst_x)) $operation='first';
							 if (isset($sbprior_x)) $operation='prior';
							 if (isset($sbnext_x)) $operation='next';
							 if (isset($sblast_x)) $operation='last';
							 if (isset($sbinsert_x)) $operation='insert';
							 if (isset($sbdelete_x)) $operation='delete';
							 if (isset($sbedit_x)) $operation='edit';
							 if (isset($sbsearch_x)) $operation='search';
							 if (isset($sbcancel_x)) $operation='cancel';
							 if (isset($sbrefresh_x)) $operation='refresh';
							 if (isset($sbdetail_x)) $operation='detail';
                         

							 if (!isset($form_action)) $form_action='';
							 


							 

							 switch($form_action)
							 {
									 case 'add':
									 {
											 $this->action='insert';
											 $this->showForm();
									 break;
									 }
									 case 'insert':
									 {
											 $closeform=$_POST['closeform'];
											 $linkfield=$_POST['linkfield'];
											 $displayfield=$_POST['displayfield'];
							 
											 $this->insert();
											 
											 if ($closeform=='1') 
											 {

												$linkvalue=$this->insertarray[$linkfield];
												$displayvalue=$this->insertarray[$displayfield];
												echo "<script language=\"JavaScript\">\n";
												echo "<!--\n";
												echo "document.cookie=\"$linkfield=$linkvalue\";\n";
												echo "document.cookie=\"$displayfield=$displayvalue\";\n";												
												echo "window.close();\n";
												echo "-->\n";				
												echo "</script>\n";
											 }
											 else
											 {
												 $this->showGrid($operation);
											 }
											 break;
									 }
									 case 'update':
									 {
											 $this->update();
											 $this->showGrid($operation);
											 break;
									 }
									 case 'lookup':
									 {
											 $this->lookup();
											 break;
									 }
									 default:  $this->showGrid($operation);
							 }
						 }
						 else
						 {
							 if ($action=='addrecord')
							 {
								 global $linkfield, $displayfield;
								 
                                 $this->hiddenfields=array('closeform'=>'1','linkfield'=>$linkfield,'displayfield'=>$displayfield,'showmenu'=>'1');
								 $this->action='insert';
								 $this->showForm();							 
							 }
							 else
							 {
								 $fc=$this->formcaption;
								 $hi=$this->helpintro;
						 
								 echo "<table align=\"left\" border=\"0\" width=\"90%\" cellspacing=\"2\" cellpadding=\"2\">";
								 echo "<tr>";
								 echo "<td colspan=\"2\"><h2>$fc</h2></td>";
								 echo "</tr>";							 
								 echo "<tr>";							 
								 echo "<td colspan=\"2\">$hi</td>";
								 echo "<tr>";
								 echo "<td colspan=\"2\">&nbsp;</td>";
								 echo "</tr>";
								 reset($this->fieldhelp);
								 while (list($f,$h)=each($this->fieldhelp))
								 {
									 echo "<tr>";		
									 $f=$this->getLabel($f);
									 echo "<td><b>$f</b></td><td>$h</td>";					
									 echo "</tr>";							 							 
								 }
								 echo "</tr>";							 
								 echo "</table>";							 
							 }
							 
							 
						}
                 }
         }
?>