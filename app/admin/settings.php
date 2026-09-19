<?php

use App\Models\Core\AdminSetting;

$data = [
    'definitions' => AdminSetting::definitions(),
    'values'      => AdminSetting::currentValues(),
];
