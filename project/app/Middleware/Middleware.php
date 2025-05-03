<?php

namespace App\Middleware;

/**
 * Base Middleware class
 */
abstract class Middleware
{
    /**
     * Handle the middleware
     * 
     * @param callable $next Next middleware in the chain
     * @return void
     */
    abstract public function handle(callable $next): void;
} 