# WP HOOKS

## Installation
    
```bash
composer require jacobprice/wp-hooks php-di/php-di
```

## Usage
```php
use Jacobprice\WpHooks\HookManager;
use DI\ContainerBuilder;

$container_builder = new ContainerBuilder();
$container = $container_builder->build();
$hook_manager = new HookManager( $container );

$hook_manager->add_source('ExampleNameSpace\\Hooks\\', __DIR__ . '/app/Hooks');

$hook_manager->register_hooks();
```


## Example Hook Class
```php
<?php

namespace ExampleNameSpace\Hooks;

use Jacobprice\WpHooks\Interfaces\Action;
use Jacobprice\WpHooks\Interfaces\Filter;

class ExampleAction implements Action, Filter{
    /**
     * Lazily load this hook
     */
    public function should_load() : bool {
        return is_admin();
    }
    /**
     * Load the hook
     */
    public function load() {
        $this->add_action('init', [$this, 'init']);
        $this->add_filter('the_content', [$this, 'the_content']);
    }

    public function init() {
        echo 'Hello, World!';
    }

    public function the_content($content) {
        return $content;
    }
}
```

