<?php

Use App\Models\Core\Propflyer;

$flyers= Propflyer::query()->orderBy('created_at', 'desc')->limit(50)->get();

dd($flyers);
