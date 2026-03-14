<?php 

// use array to prepend paths on deeper links
$prepend = [
    'search'   => 'public',];
    /* 'redis' => 'admin.cache',*/

$tmp   = strstr($renderfrom, '.', true);
$first = ($tmp !== false) ? $tmp : $renderfrom;

if (isset($prepend[$first])) {
    $pf = $prepend[$first];
    if (!str_starts_with($renderfrom, $pf . '.')) {
        $renderfrom = $pf . '.' . $renderfrom;}}