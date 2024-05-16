<?php

namespace Jacobprice\WpHooks;

Trait Filters {
    protected $filters = [];

    public function add_filter($name, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters[] = [$name, [$callback, $priority, $accepted_args]];
    }
    public function get_filters() {
        if(!$this->filters) {
            $this->load();
        };
        return $this->filters;
    }
}