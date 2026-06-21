<?php

// Validate the request data
$validatedData = $request->validate([
    'xFullStreet' => 'required|string|max:255',
    'xCity' => 'required|string|max:100',
    'xState' => 'required|string|max:2',
    'xZip' => 'required|string|max:10',
    // Add other fields as necessary
]);

echo "BEFORE REDIRECT<br>";

redirect('/member/flyer/details')->send();

echo "AFTER REDIRECT";
exit();