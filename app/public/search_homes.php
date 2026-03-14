<?php

require app_path('queries/base.php');

// clone base query
$searchAll = clone $base;

// append page-specific query logic
$searchAll = $searchAll
    ->orderBy('propflyers.created_at', 'desc')
    ->paginate(12)
    ->withQueryString();

dd($searchAll);