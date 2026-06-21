<?php

// Validate the request data
$validatedData = $request->validate([
    'xFullStreet' => 'required|string|max:255',
    'xCity' => 'required|string|max:100',
    'xState' => 'required|string|max:2',
    'xZip' => 'required|digits:5',
    // Add other fields as necessary
]);

redirect('/member/flyer/details')->send();
exit();