<?php
$dir = opendir(__DIR__);
$base = 'https://localhost/database/example';
if($dir){
    
    while(($files = readdir($dir)) !== false){
        echo '<a href="'.$base.'/'.$files.'">'.$files.'</a><br>';
    }

}else{
    echo 'Diretório não encontrado';
}