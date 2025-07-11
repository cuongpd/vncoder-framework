<?php

namespace VnCoder\Backend\Models;

class VnAdmin
{
    public static array $menuIcon = [
        'truck-fast', 'burger-soda', 'handshake', 'bag-shopping',
        'code-compare', 'signal-bars', 'face-vomit', 'alien-8bit', 'gamepad'
    ];

    public static function menuData(): array
    {
        $menuData = [];
        foreach (glob(ADMIN_PATH . 'Modules/*/*Controller.php') as $file) {
            [$module, $controller] = self::extractModuleAndController($file);
            $menuData[$module][self::formatControllerName($controller)] = self::getMethodInController($file, $controller, $module);
        }

        foreach (glob(ADMIN_PATH . 'Controllers/*Controller.php') as $file) {
            $controller = basename($file, 'Controller.php');
            if (in_array($controller, ['Admin', 'Ajax', 'Crud', 'Dashboard'])) {
                continue;
            }
            $menuData['Admin'][$controller] = self::getMethodInController($file, $controller);
        }

        return $menuData;
    }

    protected static function getMethodInController(string $file, string $controller, string $module = ''): array
    {
        $menu = [];
        $controllerLabel = self::formatControllerName($controller);
        $slugBase = $module ? toKebabCase($module) . '/' . toKebabCase($controllerLabel) . '/' : toKebabCase($controllerLabel);
        $classContent = file_get_contents($file);
        if (self::extendsCrudController($classContent)) {
            $menu[] = [
                'name' => "$controllerLabel Manager",
                'link' => $slugBase
            ];
        }
        foreach (self::extractPublicActions($classContent) as $method) {
            if (self::shouldSkipMethod($method)) continue;

            $menu[] = [
                'name' => str_replace('_', ' ', $method),
                'link' => strtolower($slugBase . ucwords(str_replace('_', '-', $method)))
            ];
        }

        return $menu;
    }

    protected static function extractModuleAndController(string $path): array
    {
        $relative = str_replace([ADMIN_PATH . 'Modules/', 'Controller.php'], '', $path);
        return explode('/', $relative);
    }

    protected static function formatControllerName(string $name): string
    {
        return trim(preg_replace('/(?=[A-Z])/', ' ', $name));
    }

    protected static function extendsCrudController(string $classContent): bool
    {
        return preg_match('/extends\s+CrudController/', $classContent);
    }

    protected static function extractPublicActions(string $classContent): array
    {
        preg_match_all('/public\s+function\s+(\w+)_Action\s*\(/', $classContent, $matches);
        return $matches[1] ?? [];
    }

    protected static function shouldSkipMethod(string $method): bool
    {
        return $method === 'Index' || preg_match('/_(Data|Edit|Ajax|Delete|Active|Info)$/i', $method);
    }
}
