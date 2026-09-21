<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->handle(Request::capture());

$sidebar = include 'config/sidebar.php';
$allRoutes = collect(Route::getRoutes())->mapWithKeys(function ($route) {
    return [$route->getName() => $route->getActionName()];
})->toArray();

$missingRoutes = [];
$missingViews = [];

function checkMenu($menu, &$missingRoutes, &$missingViews, $allRoutes)
{
    if (isset($menu['route'])) {
        $routeName = $menu['route'];
        if (! isset($allRoutes[$routeName])) {
            $missingRoutes[] = $routeName;
        } else {
            // It exists, let's try to check the controller and view
            $action = $allRoutes[$routeName];
            if ($action === 'Closure' || strpos($action, 'placeholder') !== false) {
                $missingViews[] = "$routeName (Closure/Placeholder)";
            }
        }
    }
    if (isset($menu['submenus'])) {
        foreach ($menu['submenus'] as $sub) {
            checkMenu($sub, $missingRoutes, $missingViews, $allRoutes);
        }
    }
}

foreach ($sidebar as $role => $menus) {
    foreach ($menus as $menu) {
        checkMenu($menu, $missingRoutes, $missingViews, $allRoutes);
    }
}

echo "Missing Routes:\n";
print_r(array_unique($missingRoutes));
echo "\nMissing Views / Closures:\n";
print_r(array_unique($missingViews));

// Check all controllers for placeholder views
$controllers = glob('app/Http/Controllers/*.php');
echo "\nControllers returning view('placeholder'):\n";
foreach ($controllers as $controller) {
    $content = file_get_contents($controller);
    if (strpos($content, "view('placeholder'") !== false || strpos($content, 'view("placeholder"') !== false) {
        echo basename($controller)."\n";
    }
}
