<?php

namespace App\Middleware;

/**
 * Authentication Middleware
 * Checks if a user is logged in, redirects to login page if not
 */
class AuthMiddleware extends Middleware
{
    /**
     * Handle the middleware
     * 
     * @param callable $next Next middleware in the chain
     * @return void
     */
    public function handle(callable $next): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please log in to access this page';
            header('Location: /login');
            exit;
        }
        
        // User is logged in, continue to next middleware or route handler
        $next();
    }
} 