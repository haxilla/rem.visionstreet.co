<?php

Use App\Models\Core\Propflyer;

$flyers= Propflyer::query()->orderBy('creationDate', 'desc')->limit(50)->get();

dd($flyers);
