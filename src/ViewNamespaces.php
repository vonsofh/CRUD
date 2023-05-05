<?php

namespace Backpack\CRUD;

class ViewNamespaces
{
    private static array $viewNamespaces = [];

    /**
     * Return all the view namespaces including the ones stored in the laravel config files.
     *
     * @param  string  $domain  (eg. fields, filters, buttons)
     * @return array
     */
    public static function getFor(string $domain)
    {
        $viewNamespacesFromConfig = self::getFromConfigFor($domain);

        return array_unique(array_merge($viewNamespacesFromConfig, self::getForDomain($domain)));
    }

    /**
     * Add view namespaces for a given domain.
     *
     * @param  string  $domain  (eg. fields, filters, buttons)
     * @return void
     */
    public static function addFor(string $domain, string|array $viewNamespaces)
    {
        foreach ((array) $viewNamespaces as $viewNamespace) {
            if (! in_array($viewNamespace, self::getForDomain($domain))) {
                self::$viewNamespaces[$domain][] = $viewNamespace;
            }
        }
    }

    /**
     * Return the namespaces stored for a given domain.
     */
    private static function getForDomain(string $domain)
    {
        return self::$viewNamespaces[$domain] ?? [];
    }

    /**
     * Return the array of view namespaces for backpack components from the Laravel config files.
     * It uses the default `backpack.crud.view_namespaces` key or a custom provided key.
     *
     * @return array
     */
    private static function getFromConfigFor(string $domain, mixed $customConfigKey = null)
    {
        return config($customConfigKey ?? 'backpack.crud.view_namespaces.'.$domain) ??
            [config('backpack.ui.view_namespace').$domain] ??
            [config('backpack.ui.view_namespace_fallback').$domain];
    }

    /**
     * Return all the view namespaces using a developer provided config key.
     * Allow developer to use view namespaces from other config keys.
     *
     * @param  string  $domain  (eg. fields, filters, buttons)
     * @return array
     */
    public static function getWithFallbackFor(string $domain, string $viewNamespacesFromConfigKey)
    {
        $viewNamespacesFromConfig = self::getFromConfigFor($domain, $viewNamespacesFromConfigKey);

        return array_unique(array_merge($viewNamespacesFromConfig, self::getFor($domain)));
    }

    /**
     * This is an helper function that returns the view namespace with the view name appended.
     * It's usefull to use in blade templates with `@includeFirst(ViewNamespaces::getViewPathsFor('columns', 'some_column'))`.
     *
     * @return array
     */
    public static function getViewPathsFor(string $domain, string $viewName)
    {
        return array_map(fn ($item) => $item.'.'.$viewName, self::getFor($domain));
    }
}
