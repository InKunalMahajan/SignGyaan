<?php

$path = __DIR__.'/../vendor/laravel/ai/src/Gateway/Concerns/MeasuresDuration.php';

if (! is_file($path)) {
    fwrite(STDOUT, "Laravel AI timing patch skipped: vendor file not found.\n");
    exit(0);
}

$contents = file_get_contents($path);

if ($contents === false) {
    fwrite(STDERR, "Laravel AI timing patch failed: could not read vendor file.\n");
    exit(1);
}

$patchedSignature = 'protected function elapsedMilliseconds(int|float $startedAt): float';
$legacySignature = 'protected function elapsedMilliseconds(int $startedAt): float';

if (str_contains($contents, $patchedSignature)) {
    fwrite(STDOUT, "Laravel AI timing patch already applied.\n");
    exit(0);
}

if (! str_contains($contents, $legacySignature)) {
    fwrite(STDERR, "Laravel AI timing patch not applied: expected upstream signature was not found.\n");
    exit(1);
}

$patched = str_replace($legacySignature, $patchedSignature, $contents, $count);

if ($count !== 1 || file_put_contents($path, $patched) === false) {
    fwrite(STDERR, "Laravel AI timing patch failed while writing vendor file.\n");
    exit(1);
}

fwrite(STDOUT, "Laravel AI timing patch applied for Windows hrtime() float compatibility.\n");
