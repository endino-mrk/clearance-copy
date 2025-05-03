<?php

namespace App\Middleware;

/**
 * Middleware Handler
 * Manages middleware execution
 */
class MiddlewareHandler
{
    /**
     * Execute a chain of middleware
     * 
     * @param array $middlewares List of middleware class names
     * @param callable $target The final target function to execute
     * @return void
     */
    public static function executeMiddlewareChain(array $middlewares, callable $target): void
    {
        // Create a nested callable for the middleware chain
        $next = $target;
        
        // Process middlewares in reverse to create the chain correctly
        foreach (array_reverse($middlewares) as $middlewareClass) {
            $middleware = new $middlewareClass();
            $current = $next;
            $next = function () use ($middleware, $current) {
                $middleware->handle($current);
            };
        }
        
        // Execute the chain
        $next();
    }
} 