<?php 

// use array to prepend paths on deeper links
$prepend = [
    'projects' => 'admin',
    'laravel'  => 'admin',
    'task'     => 'admin.projects',
    'comments' => 'admin.projects',
    'postgres' => 'admin.data',
    'mysql'    => 'admin.data',
    'sitemap'  => 'admin.seo',];
    /* 'redis' => 'admin.cache',*/

$tmp   = strstr($renderfrom, '.', true);
$first = ($tmp !== false) ? $tmp : $renderfrom;

if (isset($prepend[$first])) {
    $pf = $prepend[$first];
    if (!str_starts_with($renderfrom, $pf . '.')) {
        $renderfrom = $pf . '.' . $renderfrom;}}