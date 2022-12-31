<?php
$path    = __DIR__;


$files   =  array_map(
    fn($name) => ['name' => $name, 'size' => strlen(file_get_contents($name))], 
    array_diff(scandir($path), array('.', '..', 'counter.php', '.git'))
);


function cmp($a, $b)
{
    if ($a['size'] == $b['size']) {
        return 0;
    }
    return ($a['size'] < $b['size']) ? -1 : 1;
}

usort($files, "cmp");

file_put_contents($path.'/../progress/data.json', json_encode($files));