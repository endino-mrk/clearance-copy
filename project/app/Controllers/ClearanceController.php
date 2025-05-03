<?php

namespace App\Controllers;

class ClearanceController extends BaseController
{
    public function index(): void 
    { 
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Render the clearance status view
        $this->view('clearance-status/index');
    }
    
    public function create(): void { echo '<h1>ClearanceController Create</h1>'; }
    public function store(): void { echo '<h1>ClearanceController Store</h1>'; }
    public function show(): void { echo '<h1>ClearanceController Show</h1>'; }
    public function edit(): void { echo '<h1>ClearanceController Edit</h1>'; }
    public function update(): void { echo '<h1>ClearanceController Update</h1>'; }
    public function destroy(): void { echo '<h1>ClearanceController Delete</h1>'; }
}