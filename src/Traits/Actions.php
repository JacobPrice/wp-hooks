<?php

namespace Jacobprice\WpHooks\Traits;

Trait Actions {
    protected $actions = [];

    public function add_action($name, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions[] = [$name, [$callback, $priority, $accepted_args]];
    }
    public function get_actions() {
        if(!$this->actions) {
            $this->load();
        };
        return $this->actions;
    }
}