<?php

$files = [
    'resources/views/welcome.blade.php',
    'resources/views/auth/login.blade.php',
];
foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('href="#"', 'href="javascript:void(0)"', $content);
        file_put_contents($file, $content);
    }
}
echo "Fixed hrefs.\n";
