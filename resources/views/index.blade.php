<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        .flex {
            display: flex;
        }
        .flex-6 {
            flex: 6;
            background-color: lightblue;
            padding: 10px;
        }
        .flex-4 {
            flex: 4;
            background-color: lightcoral;
            padding: 10px;
        }
    </style>
    
</head>

<div class="flex">
    <div class="flex-6">LEFT SIDE</div>
    <div class="flex-4">RIGHT SIDE</div>
</div>