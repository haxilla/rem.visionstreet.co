<?php

Use App\Models\Core\Propflyer;

$flyers= Propflyer::query()->orderBy('created_at', 'desc')->get();

dd($flyers);
