<?php
namespace Jacobprice\WpHooks;

use Jacobprice\WpHooks\Traits\Actions;
use Jacobprice\WpHooks\Traits\Filters;
use Jacobprice\WpHooks\Interfaces\Loadable;

abstract class Hook implements Loadable
{
    use Actions, Filters;
}