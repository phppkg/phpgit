<?php

// require dirname(__DIR__) . '/test/bootstrap.php';

$pattern = dirname(__DIR__) . '/test/Command/Remote/*CommandTest.php';
foreach (glob($pattern) as $path) {
    $dirPath = dirname($path);
    $fileName = basename($path);
    $newName = str_replace('Command', '', $fileName);
    $newFile = $dirPath . '/' . str_replace('Command', '', $fileName);

    $contents = file_get_contents($path);
    $contents = str_replace('CommandTest', 'Test', $contents);
    file_put_contents($path, $contents);

    printf("- rename %s\n", $path);
    rename($path, $newFile);
}
