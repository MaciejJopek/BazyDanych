<?php
function sanityzacja($dane){
    $dane = trim($dane);
    $dane = stripslashes($dane);
    $dane = htmlentities($dane,ENT_NOQUOTES,"UTF-8");
    return $dane;
}
?>