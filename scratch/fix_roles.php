<?php

$dirs = ['teknisi', 'supervisor', 'pelanggan', 'marketing', 'cs', 'auditor', 'akuntan', 'admin_tenant'];
$changed = [];
foreach ($dirs as $dir) {
    $path = 'resources/views/'.$dir.'/index.blade.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, "@extends('layouts.app')") !== false && strpos($content, "@section('content')") !== false) {
            $content = str_replace("@section('content')", "@section('main_content')", $content);
            file_put_contents($path, $content);
            $changed[] = $path;
        }
    }
}
echo 'Changed files: '.implode(', ', $changed);
