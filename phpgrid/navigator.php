<?php
         if ($this->allowedto('FIRST')) echo "<input type=\"image\" name=\"sbfirst\" src=\"phpgrid/bfirst.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Primero\" title=\"Primero\"  >";
         if ($this->allowedto('PRIOR')) echo "<input type=\"image\" name=\"sbprior\" src=\"phpgrid/bprior.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Anterior\" title=\"Anterior\"  >";
         if ($this->allowedto('NEXT')) echo "<input type=\"image\" name=\"sbnext\" src=\"phpgrid/bnext.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Siguiente\" title=\"Siguiente\"  >";
         if ($this->allowedto('LAST')) echo "<input type=\"image\" name=\"sblast\" src=\"phpgrid/blast.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Último\" title=\"Último\"  >";
         if ($this->allowedto('INSERT')) echo "<input type=\"image\" name=\"sbinsert\" src=\"phpgrid/binsert.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Añadir\" title=\"Añadir\"  >";
         if ($this->allowedto('DELETE'))
         {
                 echo "<input type=\"image\" name=\"sbdelete\" src=\"phpgrid/bdelete.gif\" width=\"24\" height=\"24\" border=\"0\"";
                 if ($this->confirmdelete)
                 {
                         echo " onclick=\"return(confirmDelete());\"";
                 }
                 echo " alt=\"Borrar\" title=\"Borrar\" >";
         }
         if ($this->allowedto('SEARCH')) echo "<input type=\"image\" name=\"sbsearch\" src=\"phpgrid/bsearch.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Buscar\" title=\"Buscar\"  >";
//         if ($this->allowedto('DETAIL')) echo "<input type=\"image\" name=\"sbdetail\" src=\"phpgrid/bprint.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Imprimir registro\" title=\"Imprimir registro\"  >";
         if ($this->allowedto('HELP')) echo "<a href=\"javascript:showHelpWindow();\" alt=\"Ayuda\" title=\"Ayuda\"><img src=\"phpgrid/help.gif\" width=\"24\" height=\"24\" border=\"0\"></a>";
		                                     

?>