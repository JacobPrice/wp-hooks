<?php

namespace Jacobprice\WpHooks\Interfaces;


interface Loadable {    
    /**
     * should_load
     *
     * Returns a boolean value to determine if the class should be loaded
     * 
     * Example usage:
     * public function should_load() {
     *  return is_admin();
     * }
     * @return bool
     */
    public function should_load() : bool;
    public function load();
}

