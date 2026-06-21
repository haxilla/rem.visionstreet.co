<?php

$flyerId = (int)($parts[3] ?? 0);

if (!$flyerId) {
    dd("Flyer ID is missing or invalid.");
}

dd("Flyer ID: $flyerId");