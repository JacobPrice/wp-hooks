<?php

namespace Jacobprice\WpHooks;

use DI\Container;
use Jacobprice\WpHooks\ActionInterface;
use Jacobprice\WpHooks\FilterInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use CallbackFilterIterator;

class HookManager
{
    private array $namespace_and_path = [];

    public function __construct(private Container $container)
    {
    }

    public function add_source(string $namespace, string $path)
    {
        $this->namespace_and_path[] = ['namespace' => $namespace, 'path' => $path];
    }

    public function register_hooks()
    {
        foreach ($this->namespace_and_path as $source) {
            $this->iterate_over_hooks($source['namespace'], $source['path']);
        }
    }

    private function iterate_over_hooks(string $namespace, string $path)
    {
        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new CallbackFilterIterator($iterator, function ($current) {
            return $current->isFile() && $current->getExtension() === 'php';
        });

        foreach ($phpFiles as $file) {
            $relative_path_namespace = $this->path_to_namespace($file->getPathname(), $path);
            $hook_class = $namespace . $relative_path_namespace;
            $hook_class = str_replace('/', '\\', $hook_class);

            $instance = $this->container->get($hook_class);

            if ($instance instanceof ActionInterface && $instance->should_load()) {
                $this->registerActions($instance);
            }
            if ($instance instanceof FilterInterface && $instance->should_load()) {
                $this->registerFilters($instance);
            }
        }
    }

    private function path_to_namespace(string $full_path, string $base_path): string
    {
        $relative_path = str_replace($base_path, '', $full_path);
        $relative_path = str_replace('.php', '', $relative_path);
        $relative_path = trim($relative_path, DIRECTORY_SEPARATOR);
        $source = str_replace(DIRECTORY_SEPARATOR, '\\', $relative_path);

        return $source;
    }

    private function registerActions(ActionInterface $object)
    {
        $actions = $object->get_actions();
        foreach ($actions as $actionDetails) {
            $callback = $actionDetails[1][0];
            if (!is_callable($callback)) {
                throw new \Exception('Invalid callback for action: ' . $actionDetails[0], 1);
            }
            add_action(
                $actionDetails[0],
                $callback,
                $actionDetails[1][1] ?? 10,
                $actionDetails[1][2] ?? 1 
            );
        }
    }

    private function registerFilters(FilterInterface $object)
    {
        $filters = $object->get_filters();
        foreach ($filters as $filterDetails) {
            $callback = $filterDetails[1][0];
            if (!is_callable($callback)) {
                throw new \Exception('Invalid callback for filter: ' . $callback, 1);
            }
            add_filter(
                $filterDetails[0],
                $callback,
                $filterDetails[1][1] ?? 10,
                $filterDetails[1][2] ?? 1 
            );
        }
    }
}
