<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    /**
     * Helper function to render a PHP view file.
     */
    protected function view(string $view, array $data = []): void
    {
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

    /**
     * Display the dashboard
     */
    public function index(): void
    {
        // Ensure user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Include helper functions
        require_once BASE_PATH . '/app/helpers.php';
        
        // Render the dashboard view
        $this->view('dashboard/index');
    }
} 