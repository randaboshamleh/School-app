<?php

$directory = __DIR__; // ابحث في كل المشروع
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $contents = file_get_contents($file->getPathname());
        if (strpos($contents, 'class RoleMiddleware') !== false) {
            echo 'FOUND RoleMiddleware in: '.$file->getPathname().PHP_EOL;
        }
        if (strpos($contents, 'class RoleOrPermissionMiddleware') !== false) {
            echo 'FOUND RoleOrPermissionMiddleware in: '.$file->getPathname().PHP_EOL;
        }
    }
}
