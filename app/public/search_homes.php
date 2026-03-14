<?php

require app_path('queries/base.php');

// clone base query
$searchAll = clone $base;

// append page-specific query logic
$searchAll = $searchAll
    ->with(['thePhotos' => function ($q) {
        $q->select('propflyer_id', 'photoName', 'def');
    }])
    ->with(['theAgent' => function ($q) {
        $q->select('id', 'agtFullName', 'agtPhoto', 'agtMainPhone', 'agtLogo')
          ->with(['theAgentCleanup' => function ($q) {
              $q->select('propagent_id', 'newRemID');
          }]);
    }])
    ->orderBy('propflyers.created_at', 'desc')
    ->paginate(12)
    ->withQueryString();

$data = [
    'searchAll' => $searchAll,
];