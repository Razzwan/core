<?php
function print_var($var, $flag = true){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if($flag)exit;
}
