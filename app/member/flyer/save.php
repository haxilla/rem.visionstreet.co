<?php

// Validate the request data
$validatedData = $request->validate([
    'address' => 'required|string|max:255',
    'city' => 'required|string|max:100',
    'state' => 'required|string|max:2',
    'zip' => 'required|string|max:10',
    // Add other fields as necessary
]);

if ($validator->fails()) {

    dd($validator->errors()->toArray());

}

echo "BEFORE REDIRECT<br>";

redirect('/member/flyer/details')->send();

echo "AFTER REDIRECT";
exit();