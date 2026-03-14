<?php

// Validate segments & depth
foreach ($parts as $seg) {
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $seg)) {
        throw new \InvalidArgumentException("Invalid segment: $seg");}}

// max parts check
if (count($parts) > self::MAX_SEGMENTS) {
    throw new \RuntimeException('Too many path segments (max ' . self::MAX_SEGMENTS . ').');}

//standardize to $renderfrom
$renderfrom = $parts ? implode('.', $parts) : '';
//prepend if needed
include(app_path().'/code/prepend_prefix.php');
// compute view path + run processor
$renderPath = str_replace('.', '/', $renderfrom);
/**
 * View candidates:
 * - Always try the flat view "<a.b.c>"
 * - Also try one-level-deeper implicit index "<a.b.c.index>"
 */
$candidates = [];
if ($renderfrom !== '') {
    // e.g. "admin.data.postgres"
    $candidates[] = $renderfrom;
    // e.g. "admin.data.postgres.index"
    $candidates[] = "$renderfrom.index";  
    // e.g. "admin.data.postgres.show"
    $candidates[] = "$renderfrom.show";}

$viewName = null;
foreach ($candidates as $cand) {
    if ($cand !== '' && view()->exists($cand)) {
        $viewName = $cand;
        break;}}

/**
 * App file candidates (GET):
 * - Try flat app file ".../<path>.php"
 * - Then implicit index ".../<path>/index.php"
 * This mirrors the view fallback so your PHP app layer stays in sync.
 */
$appCandidates = [
    app_path("$renderPath.php"),
    app_path("$renderPath/index.php"),];

dd($appCandidates);

$data = null;
foreach ($appCandidates as $p) {
    if (is_file($p))  { include $p; break; }}

if (!$viewName) {
    throw new 
    \RuntimeException(
        "View not found for path: '$renderfrom'"
    );} 