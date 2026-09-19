<?php

// Shared area-key mapping between the member send-setup form's area
// keys and the DB/admin side's keys (matching the rememaildb table
// names and app/admin/flyer/camps.php's display labels), so the two
// naming schemes can't drift apart.

return [
    'phoenix_metro'    => ['db' => 'azphxmetro', 'label' => 'Phoenix Metro'],
    'northeast_valley' => ['db' => 'azphxne',    'label' => 'Phoenix Northeast'],
    'southeast_valley' => ['db' => 'azphxse',    'label' => 'Phoenix Southeast'],
    'west_valley'      => ['db' => 'azphxwv',    'label' => 'Phoenix West Valley'],
    'northern_az'      => ['db' => 'aznaz',      'label' => 'North Arizona'],
    'southern_az'      => ['db' => 'azsaz',      'label' => 'South Arizona'],
];
