<?php
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
                * Table to manager
                * @var string
                */
                var $table;

                /**
                * Edit/Insert action atribute
                * @var string
                */
                var $action='';
				
				var $synchronize=array();
				
				var $synchronizefields=array();


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
                 var $units='registros';

                 /**
                 *No editable fields
                 */
                 var $noeditable=array();

                 /**
                 *No insertable fields
                 */
                 var $noinsertable=array();

                 /**
                 *Wheter shows or not the statusbar
                 */
                 var $showstatus=1;

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
                 *Array of field value functions
                 */
                 var $getfieldvaluefunctions=array();
				 
				 /**
				 *Auxiliar result set
				 */
				 var $rs;
				 
                 /**
                 *Colors for the grid
                 */
                 var $colors=array('#F0F0F0','#E0E0E0','#B6C3F2','#ABCEE9');

                 /**
                 *Ask for confirmation before delete a record
                 */
                 var $confirmdelete=true;

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
                 var $allowedactions=array('FIRST'=>'1','PRIOR'=>'1','NEXT'=>'1','LAST'=>'1', 'SEARCH'=>'1', 
                                           'INSERT'=>'1','DELETE'=>'1','EDIT'=>'1');

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
                 *@param object $rs Recordset where the field object belongs
                 *@param string $value Current field value
                 *@return string
                 */
                 function getControl($fieldobject,$rs,$value='')
                 {
                         $fieldname=$fieldobject->name;
                         $fieldsize=$fieldobject->max_length;
                         if (array_key_exists($fieldname,$this->controls))
                         {
                                 $controlarray=$this->controls[$fieldname];
                                 list($controltype,$parameters)=each($controlarray);
                                 switch($controltype)
                                 {
                                         case 'edit':
                                         {

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

                                                 $control="<input type=\"radio\" name=\"$fieldname\" value=\"$value\">";
                                                 break;
                                         }
                                         case 'checkbox':
                                         {

                                                 $control="<input type=\"checkbox\" name=\"$fieldname\" value=\"$value\">";
                                                 break;
                                         }
                                         case 'combo':
                                         {

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

                                                 if (array_key_exists('function',$parameters))
                                                 {
                                                         $displayfunction=$parameters['function'];
                                                         eval ("\$control=$displayfunction(\$fieldobject,\$value);");
                                                 }
                                                 break;
                                         }
                                         case 'list':
                                         {

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
                         else if (array_key_exists($fieldobject->name,$this->relations))
                         {
                                 $params=$this->relations[$fieldobject->name];
                                 $detailtable=$params['detailtable'];
                                 $linkfield=$params['linkfield'];
                                 $displayfield=$params['displayfield'];
                                 $refpage=$params['refpage'];
								 $filter=$params['filter'];

                                 if (trim($value)=='') $value=$rs->fields[$fieldobject->name];

                                 $control=$this->getFieldValue($fieldobject,$value);
                                 $fieldlabel=$this->getLabel($fieldobject);
                                 if ($refpage!='')
                                 {
                                          $control.="<A HREF=\"$refpage?sbinsert_x=5&amp;$linkfield=$value&amp;where=$linkfield&amp;wherevalue=$value\" title=\"Añadir\"><img src=\"binsert.gif\" border=\"0\"></A>";
                                          $control.="<A HREF=\"$refpage?$linkfield=$value&amp;where=$linkfield&amp;wherevalue=$value\" title=\"Editar\"><img src=\"bedit.gif\" border=\"0\"></A>";
                                          $control="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\">$control</td></tr></table>";
                                 }
                                 else
                                 {
                                         $control=$this->getTableCombo($fieldobject->name,$detailtable,$linkfield,$displayfield,$value,$filter);
                                 }
                         }
                         else
                         {
                                 if ($fieldsize>$this->maxfieldsize) $fieldsize=$this->maxfieldsize;
                                 $maxsize=$fieldobject->max_length;

                                 //TODO: Allow the user to choose which control wants for each field
                                 if ($fieldobject->blob)
                                 {
                                         $control="<textarea name=\"$fieldname\" cols=\"$this->defaultcols\" rows=\"$this->defaultrows\">$value</textarea>";
                                 }
                                 else
                                 {
                                         $control="<input type=\"text\" name=\"$fieldname\" value=\"$value\" size=\"$fieldsize\" maxlength=\"$maxsize\">";
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
                         $sql = "select $lookupfield,$displayfield from $detailtable order by $displayfield";
                         $rs=$this->ExecSQL($sql);

                         $control="<select name=\"$masterfield\">";
                         while (!$rs->EOF)
                         {
								 $add=true;
								 if ($filter!='')
								 {
									eval ("\$add=$filter(\$rs);");								 
							     }
								 if ($add==true)
								 {
									 $value=$rs->fields[$lookupfield];
									 $display=$rs->fields[$displayfield];
									 $control.="<option value=\"$value\">$display</option>";
								 }

                                 $rs->MoveNext();
                         }
                         $control.="</select>";
                         $control=str_replace("value=\"$selected\"","value=\"$selected\" selected",$control);
                         return($control);
                 }


                 /**
                 *To get the label of a field
                 *
                 *Returns a label for a field if is set in the labels array, if not returns the field name
                 *
                 *@param object $fieldobject Field object to get the label
                 *@return string
                 */
                 function getLabel($fieldobject)
                 {
                         $fname=$fieldobject->name;
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
                         if (!$this->allowedto('EDIT'))
                         {
                                 echo "No le está permitido realizar esta acción [EDIT]";
                                 exit;
                         }

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

                         $fields=$this->getFields();

                         $filters='';
                         $cond='';

                         for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
                                 if (array_key_exists($field->name,$_POST))
                                 {
                                         $value=$_POST[$field->name];
										 
										 switch($field->type)
										 {
											 case 'date':
											 {
												 $pieces=explode('/',$value,3);
												 if (count($pieces)!=3) $pieces=explode('-',$value,3);
												 $value="$pieces[2]/$pieces[1]/$pieces[0]";
												 break;
											 }
										 }
										 

                                         if ($i==0)
                                         {
                                                 $cond= "$field->name='$value'";
                                         }
                                         else
                                         {
												if ($filters!='') $filters.=' , ';
                                                $filters.="$field->name='$value'";
                                         }
                                 }

                         }

                         $sql = "UPDATE $this->table SET $filters WHERE $cond";

//                         phpinfo();
//       					 echo $sql;
						 
						 $this->ExecSQL($sql);

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
                         if (!$this->allowedto('DELETE'))
                         {
                                 echo "No le está permitido realizar esta acción [DELETE]";
                                 exit;
                         }

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

                         $fields=$this->getFields();

                         $filters='';
                         $cond='';

                         global $currentrecord,$selectedrow;

                         $sql = "select * from $this->table";
                         $sql=$this->filterquery($sql);
                         $field=$fields[0];

                         $values=array();

                         $i=0;

                         while (list($k,$v)=each($selectedrow))
                         {
                                 $rs = $this->SelectLimit($sql,1,$currentrecord+$k);
                                 $values[$i]=$rs->fields[$field->name];
                                 $i++;
                         }

                         for ($i=0;$i<=count($values)-1;$i++)
                         {
						 
								 $thisvalues=$this->getfieldsvalues($field->name,$values[$i]);
                 						 
                                 $sql = "DELETE FROM $this->table WHERE $field->name='$values[$i]'";

                                 $this->ExecSQL($sql);
								 
								 if ($this->afterdelete!='')
								 {
									 $event=$this->afterdelete;
									 $sender=$this;
									 eval ("$event(\$sender,\$thisvalues);");
							 }								 
                         }
                         
                 }

                 /**
                 *Delete a table
                 */
                 function emptytable()
                 {
                         if (!$this->allowedto('DELETE'))
                         {
                                 echo "No le está permitido realizar esta acción [DELETE]";
                                 exit;
                         }


                         $this->connect();

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $sql = "DELETE FROM $this->table";

                         $this->ExecSQL($sql);
                 }

                 /**
                 *Inserts a new record in the table
                 */
                 function insert()
                 {

                         if (!$this->allowedto('INSERT'))
                         {
                                 echo "No le está permitido realizar esta acción [INSERT]";
                                 exit;
                         }

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

                         for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
                                 if (array_key_exists($field->name,$_POST))
                                 {
                                         if ($fieldnames!='') $fieldnames.=', ';
                                         $fieldnames.=$field->name;

                                         if ($fieldvalues!='') $fieldvalues.=', ';
                                         $value=$_POST[$field->name];
                                         $fieldvalues.="'$value'";

                                         if ($wherecond!='') $wherecond.=' and ';
                                         $wherecond.=" $field->name='$value' ";
                                 }
                         }

                         $sql = "insert into $this->table($fieldnames) values ($fieldvalues)";

                         $this->ExecSQL($sql);

                         if ($this->afterinsert!='')
                         {
                                 $event=$this->afterinsert;
                                 $sender=$this;
                                 $insertarray=array();
                                 $sql = "select * from $this->table where $wherecond";
                                 $rs=$this->ExecSQL($sql);

                                 for ($i=0;$i<=($rs->FieldCount())-1;$i++)
                                 {
                                         $field=$rs->FetchField($i);
                                         $insertarray[$field->name]=$rs->fields[$field->name];
                                 }
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

                         $sql = "insert into $this->table($fieldnames) values ($fieldvalues)";

                         $this->ExecSQL($sql);

                 }
				 
                 function deleterecord($fields)
                 {
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

                         $sql = "delete from $this->table where $where";

                         $this->ExecSQL($sql);

                 }
				 

                 /**
                 *Output a row of the grid
                 *
                 *@param array $row The row contents
                 *@param integer $rownum Number of the row
                 *@param array $rcolors Colors of each cell
                 */
                 function gridRow($row,$rownum,$rcolors)
                 {
                         global $PHP_SELF;
                         $rowcolor=$this->colors[0];
                         echo "<tr bgcolor=\"$rowcolor\">\n";
                         if ($this->showcheckboxes)
                         {
                                  echo "<td>";
                                  echo "<input type=\"checkbox\" name=\"selectedrow[$rownum]\">";
                                  echo "</td>\n";
                         }
                         for ($i=0;$i<=count($row)-1;$i++)
                         {
                                 $color=$rcolors[$i];
                                 echo "<td $color>$row[$i]</td>\n";
                         }
                         echo "</tr>\n";
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
                         if (isset($where))
                         {
                                 $where=rawurldecode($where);
                                 if (trim($where!='')) $sql.=" where $where";
                         }
                         if (isset($wherevalue))
                         {
                                 if (trim($wherevalue)!='') $sql.="=$wherevalue";
                         }

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
                                         $currentrecord-=$this->recordsperpage;
                                         if ($currentrecord<0) $currentrecord=0;
                                         break;
                                 }

                                 case 'first':
                                 {
                                         $currentrecord=0;
                                         break;
                                 }

                                 case 'last':
                                 {
                                         $sql = "select * from $this->table";
                                         $sql=$this->filterquery($sql);
                                         $rs = $this->ExecSQL($sql);
                                         $currentrecord=floor($rs->RowCount() / $this->recordsperpage) * $this->recordsperpage;
                                         if ($currentrecord>=$rs->RowCount()) $currentrecord=$rs->RowCount()-$this->recordsperpage;
                                         break;
                                 }
                                 case 'insert':
                                 {
                                         $this->action='insert';
                                         $this->showForm();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'delete':
                                 {
                                         $this->delete();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'edit':
                                 {
                                         list($k,$v)=each($selectedrow);
                                         $this->action='update';

                                         $this->showForm($currentrecord+$k);

                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                                 case 'search':
                                 {
                                         $this->action='lookup';
                                         $this->showForm();
                                         $this->showGrid('');
                                         exit;
                                         break;
                                 }
                         }

                         $sql = "select * from $this->table";

                         $sql=$this->filterquery($sql);

                         $rs = $this->SelectLimit($sql,$this->recordsperpage,$currentrecord);

                         $this->grid($rs,$currentrecord,true);

                 }

                 /**
                 *Returns the value for a field
                 *
                 *Depending on the field's datatype, it formats the value or calls a custom function to get the value
                 *
                 *@param object $field The field object
                 *@param mixed $value Value for the field
                 *@return string
                 */
                 function getFieldValue($field,$value)
                 {
                         if (array_key_exists($field->name,$this->relations))
                         {
                                 $params=$this->relations[$field->name];
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
                                                 eval ("\$value=$displayfunction(\$rs);");
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
												 $tim=strtotime($value);
												 $value=date('d/m/Y',$tim);
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
                 function grid($rs,$currentrecord=0,$fullrs=true)
                 {
                         if (!$this->showgrid) exit;
                         global $PHP_SELF;
						 
						 $fname=$this->forname."grid";

                         $fields=$rs->FieldCount();

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
                         for ($i=0;$i<=$fields-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 if (!array_key_exists($field->name,$this->novisible))
                                 {
                                         $colspan++;
                                 }
                         }

                         $colspan+=count($this->calculatedfields);

                         $w='';
                         if ($this->autosize) $w='width="100%"';

                         $border='border="0"';

                         echo "<table $border $w bgcolor=\"#ffffff\">\n";
                         if ($this->caption!='')
                         {
                                  echo "<tr bgcolor=\"$headercolor\">\n";
                                  if (!$this->showcheckboxes) $colspan--;
                                 echo "<td colspan=\"$colspan\"><b>$this->formcaption</b></td>\n";
                                 echo "</tr>\n";
                         }
                         echo "<tr bgcolor=\"$headercolor\">\n";
                         if ($this->showcheckboxes)
                         {
                                  echo "<td>";
                                  echo "<input type=\"checkbox\" name=\"checkall\" onClick=\"CheckAllBoxes()\">";
                                  echo "</td>\n";
                         }

                         $rcolors=array();
                         for ($i=0;$i<=$fields-1;$i++)
                         {
                                 $field=$rs->FetchField($i);
                                 $cellcolor='';
                                 if (!array_key_exists($field->name,$this->novisible))
                                 {

                                         $label=$this->getLabel($field);
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
                                                 $where=rawurldecode($where);
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
                         echo "<tr>\n";
                         $r=0;
                         while (!$rs->EOF)
                         {
                                 $row=array();
                                 $k=0;
                                 for ($i=0;$i<=$fields-1;$i++)
                                 {
                                         $field=$rs->FetchField($i);
                                         if (!array_key_exists($field->name,$this->novisible))
                                         {
                                                 $row[$k]=$this->getFieldValue($field,$rs->fields[$i]);

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

                                 $this->gridRow($row,$r,$rcolors);

                                 $rs->MoveNext();
                                 $r++;
                         }
                         echo "</tr>\n";

                         if ($this->showstatus)
                         {
                                  echo "<tr bgcolor=\"$headercolor\">\n";

                                 $sql = "select * from $this->table";

                                 $sql=$this->filterquery($sql);
                                 if ($fullrs) $rs = $this->ExecSQL($sql);
                                 $totalrecords=$rs->RowCount();
                                 $currentrecord++;

                                  $recs="$currentrecord de $totalrecords";
                                 if (!$fullrs)
                                 {
                                         $recs="$totalrecords $this->units";
                                  }

                                 if (!$this->showcheckboxes) $colspan--;
                                 echo "<td align=\"right\" colspan=\"$colspan\">$recs</td>\n";
                                  echo "</tr>\n";
                         }

                         if ($this->shownavigator)
                         {
                                 echo "<tr bgcolor=\"$headercolor\">\n";

                                 if (!$this->showcheckboxes) $colspan--;
                                 echo "<td align=\"right\" colspan=\"$colspan\">\n";
                                 include "navigator.php";
                                 echo "</td>\n";
                                 echo "</tr>\n";
                         }
                         echo "</table>\n";
                         if ($this->shownavigator)
                         {
                                  echo "</form>\n";
                         }
                 }

                 /**
                 *Connect to the database
                 *
                 *This function connects to the database and stored the connection on {@link $conn}
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
                         global $where;

                         $this->lasterror=0;
                         $this->lasterrmsg='';

                         $fields=$this->getFields();

                         $filters='';

                         for ($i=0;$i<=count($fields)-1;$i++)
                         {
                                 $field=$fields[$i];
                                 if (array_key_exists($field->name,$_POST))
                                 {

                                         $value=trim($_POST[$field->name]);

                                         if ($value!='')
                                         {
                                                 if ($filters!='') $filters.=' AND ';

                                                 if (array_key_exists($field->name,$this->relations))
                                                 {
                                                          $filters.="$field->name = '$value'";
                                                 }
                                                 else
                                                 {
                                                          $filters.="$field->name LIKE '%$value%'";
                                                 }
//                                                 $filters.="$field->name LIKE '%$value%'";
                                         }
                                 }
                         }


                         $sql = "select * from $this->table where $filters";
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

                         $rs = $this->ExecSQL($sql);

                         $where=$filters;
                         $this->grid($rs);

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

                         $sql=$this->filterquery($sql);

                         if (!$showvalues) $rs = $this->ExecSQL($sql);
                         else
                         {
                                 $rs = $this->SelectLimit($sql,1,$record);
                         }


                         $fields=$rs->FieldCount();

                         OpenTable();
                         $onsubmit='';
                         if ($this->action!='lookup')
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

                         if ($this->formcaption!='')
                         {
                                 $caption=$this->formcaption;
                                 switch($this->action)
                                 {
                                         case 'insert': $caption.=' - Añadir nuevo';
                                                        break;
                                         case 'update': $caption.=' - Modificar';
                                                        break;
                                         case 'lookup': $caption.=' - Buscar';
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

                         while (list($k,$v)=each($thefields))
                         {
//******************************************************************************
                                 $field=$fieldobjects[$v];

                                 $fname=$field->name;
                                 if ($showvalues)        //Edit a record
                                 {
                                         if (!array_key_exists($fname,$this->noeditable))
                                         {
                                                 $value=$rs->fields[$field->name];
												 //HERE
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
								 
                                                 $control=$this->getControl($field,$rs,$value);
                                                 $label=$this->getLabel($field);
                                                 if ($this->usecolons) $label=$label.':';

                                                 echo "<tr>\n";
                                                 echo "<td>";
                                                 echo "$label";

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
										 if ((array_key_exists($fname,$this->novisible)) && (array_key_exists($fname,$this->noinsertable)))
												 {
													 $value=$rs->fields[$field->name];
													 echo "<input type=\"hidden\" name=\"$fname\" value=\"$value\">\n";
												 }																						 

                                 }
                                 else
                                 {
                                         $value='';
                                         if (array_key_exists($field->name,$GLOBALS))
                                         {
                                                 $value=$GLOBALS[$field->name];
                                         }

                                         if (!array_key_exists($fname,$this->noinsertable))
                                         {
                                                 if ($this->action=='lookup')
                                                 {
                                                         if (array_key_exists($fname,$this->relations))
                                                         {
                                                                 continue;
                                                         }
                                                 }

                                         $control=$this->getControl($field,$rs,$value);

                                         if ($this->action=='insert')
                                         {
                                                 if (array_key_exists($fname,$this->relations))
                                                 {
                                                         if ($control=='') continue;
                                                         if ($this->relations[$fname]['displayfunction']!='') continue;
                                                 }
                                         }

                                         $label=$this->getLabel($field);
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
//******************************************************************************
                         $submit="<INPUT TYPE=\"Submit\" VALUE=\"Aceptar\">";
                         $reset="<INPUT TYPE=\"Reset\" VALUE=\"Cancelar\">";

                         echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
                         echo "<tr bgcolor=\"$headercolor\"><td>&nbsp;</td><td align=\"right\">$submit&nbsp;$reset</td>\n";
                         $required='';
                         if ($this->action!='lookup')
                         {
                                 $required='(*) Campo requerido';
                         }
                         echo "<tr bgcolor=\"$headercolor\"><td>&nbsp;</td><td align=\"right\">$required</td>\n";
                         echo "</table>\n";
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
							 if ($this->EOF) return(array());
							 $this->rs->MoveNext();
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
								 if ($relations==TRUE) $value=$this->getFieldValue($field,$value);
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
<script language="JavaScript">
<!--
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
         if (confirm("¿Está seguro que quiere borrar los registros seleccionados?"))
         {
                 return(true);
         }
         else
         {
                 return(false);
         }
}
//-->
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
                                         //Este código es una mierda, cuando tenga más tiempo lo arreglaré
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
                 *Main method
                 *
                 *This function manages all the operations for the grid, in normal conditions you don't need to call another method
                 *
                 */
                 function run()
                 {
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
                         

                         if (!isset($form_action)) $form_action='';

                         switch($form_action)
                         {
                                 case 'add':
                                 {
                                         $this->action='insert';
                                         $this->showForm();
                                 break;
                                 }
                                 /*
                                 case 'search':
                                 {
                                         $this->action='lookup';
                                         $this->showForm();
                                 break;
                                 }
                                 */
                                 case 'insert':
                                 {
                                         $this->insert();
                                         $this->showGrid($operation);
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
         }
?>