<?php
         if ($this->allowedto('FIRST')) echo "<input type=\"image\" name=\"sbfirst\" src=\"phpgrid/images/bfirst.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Primero\" title=\"Primero\"  >";
         if ($this->allowedto('PRIOR')) echo "<input type=\"image\" name=\"sbprior\" src=\"phpgrid/images/bprior.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Anterior\" title=\"Anterior\"  >";
         if ($this->allowedto('NEXT')) echo "<input type=\"image\" name=\"sbnext\" src=\"phpgrid/images/bnext.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Siguiente\" title=\"Siguiente\"  >";
         if ($this->allowedto('LAST')) echo "<input type=\"image\" name=\"sblast\" src=\"phpgrid/images/blast.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"�ltimo\" title=\"�ltimo\"  >";
         if ($this->allowedto('INSERT')) echo "<input type=\"image\" name=\"sbinsert\" src=\"phpgrid/images/binsert.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"A�adir\" title=\"A�adir\"  >";
         if ($this->allowedto('DELETE'))
         {
                 echo "<input type=\"image\" name=\"sbdelete\" src=\"phpgrid/images/bdelete.gif\" width=\"24\" height=\"24\" border=\"0\"";
                 if ($this->confirmdelete)
                 {
                         echo " onclick=\"return(confirmDelete());\"";
                 }
                 echo " alt=\"Borrar\" title=\"Borrar\" >";
         }
         if ($this->allowedto('EDIT')) echo "<input type=\"image\" name=\"sbedit\" src=\"phpgrid/images/bedit.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Editar\" title=\"Editar\"  >";
         if ($this->allowedto('SEARCH')) echo "<input type=\"image\" name=\"sbsearch\" src=\"phpgrid/images/bsearch.gif\" width=\"24\" height=\"24\" border=\"0\" alt=\"Buscar\" title=\"Buscar\"  >";
?>