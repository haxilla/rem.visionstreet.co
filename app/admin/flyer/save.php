<?php

// Validate the request data
$validatedData = $request->validate([
    'address' => 'required|string|max:255',
    'city' => 'required|string|max:100',
    'state' => 'required|string|max:2',
    'zip' => 'required|string|max:10',
    // Add other fields as necessary
]);

dd($validatedData); // Dump and die to inspect the validated data