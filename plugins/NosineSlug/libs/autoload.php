<?php
spl_autoload_register(function ($class) {
    $prefixes = [
        'Symfony\\Component\\String\\' => __DIR__ . '/symfony-string/',
        'Symfony\\Contracts\\Translation\\' => __DIR__ . '/symfony-translation-contracts/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) !== 0) continue;

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
