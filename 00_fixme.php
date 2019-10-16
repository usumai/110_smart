<?php
$addr_git= ' "\Program Files\Git\bin\git"  ';
$output  = shell_exec($addr_git.' init 2>&1'); 
$output .= shell_exec($addr_git.' clean  -d  -f .');
$output .= shell_exec($addr_git.' reset --hard');  
$output .= shell_exec($addr_git.' pull https://github.com/usumai/110_smart.git');
echo "<pre>$output</pre>";


// header("Location: index.php");

?>