function showlookup(str_lookup,str_value,str_desc,str_params) 
{
        var vLookup = window.open(str_lookup+"?action=addrecord&linkfield="+str_value+"&displayfield="+str_desc, "lookup", "resizable=no,scrollbars=no,status=no,menubar=no,top=200,left=200,"+str_params);

        vLookup.opener = self;
}
