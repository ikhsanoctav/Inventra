<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->handle(Request::capture());

$sidebar = include 'config/sidebar.php';
$allRoutes = collect(Route::getRoutes())->mapWithKeys(function ($route) {
    return [$route->getName() => $route];
})->toArray();

function checkMenu($menu, &$missingViews, $allRoutes)
{
    if (isset($menu['route'])) {
        $routeName = $menu['route'];
        if (isset($allRoutes[$routeName])) {
            $route = $allRoutes[$routeName];
            $action = $route->getActionName();

            // If action is a controller method
            if (strpos($action, '@') !== false) {
                [$controllerClass, $method] = explode('@', $action);
                try {
                    $reflector = new ReflectionMethod($controllerClass, $method);
                    $filename = $reflector->getFileName();
                    $start_line = $reflector->getStartLine() - 1;
                    $end_line = $reflector->getEndLine();
                    $length = $end_line - $start_line;

                    $source = file($filename);
                    $body = implode('', array_slice($source, $start_line, $length));

                    if (preg_match("/view\(['\"]([^'\"]+)['\"]/", $body, $matches)) {
                        $viewName = $matches[1];
                        if ($viewName === 'placeholder') {
                            $missingViews[] = "$routeName -> returns view('placeholder')";
                        } else {
                            // Check if the blade file exists
                            $viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
                            if (! file_exists($viewPath)) {
                                $missingViews[] = "$routeName -> missing view file ($viewPath)";
                            }
                        }
                    }
                } catch (Exception $e) {
                    $missingViews[] = "$routeName -> Reflection failed: ".$e->getMessage();
                }
            }
        }
    }
    if (isset($menu['submenus'])) {
        foreach ($menu['submenus'] as $sub) {
            checkMenu($sub, $missingViews, $allRoutes);
        }
    }
}

$missingViews = [];
foreach ($sidebar as $role => $menus) {
    foreach ($menus as $menu) {
        checkMenu($menu, $missingViews, $allRoutes);
    }
}

echo "Missing Views Results:\n";
print_r(array_unique($missingViews));
