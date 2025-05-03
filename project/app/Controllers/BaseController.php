<?php

namespace App\Controllers;

/**
 * Base Controller
 * 
 * All controllers should extend this class to inherit common functionality
 */
class BaseController
{
    /**
     * Helper function to render a PHP view file.
     */
    protected function view(string $view, array $data = []): void
    {
        // Include helper functions before rendering any view
        if (file_exists(BASE_PATH . '/app/helpers.php')) {
            require_once BASE_PATH . '/app/helpers.php';
        }
        
        $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Error: View file not found at {$viewFile}";
            error_log("View file not found: {$viewFile}");
            return;
        }
        extract($data);
        ob_start();
        try {
            include $viewFile;
        } catch (\Throwable $e) {
            ob_end_clean();
            http_response_code(500);
            echo "Error rendering view: " . $e->getMessage();
            error_log("View rendering error in {$viewFile}: " . $e->getMessage());
            return;
        }
        echo ob_get_clean();
    }
}