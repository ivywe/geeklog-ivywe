<?php

function COMJ_dlfile($file)
{
    global $_CONF;
    header ("Content-Disposition: attachment; filename=$file");
    header ("Content-type: application/x-csv");
    readfile ($_CONF['path_data'].$file);

    return;
}


?>
