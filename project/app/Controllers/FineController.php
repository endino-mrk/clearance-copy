<?php

namespace App\Controllers;

class FineController extends BaseController
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
        
        // Render the fines view
        $this->view('fines/index');
    }
    
    public function create(): void { echo '<h1>FineController Create</h1>'; }
    public function store(): void { echo '<h1>FineController Store</h1>'; }
    public function show(): void { echo '<h1>FineController Show</h1>'; }
    public function edit(): void { echo '<h1>FineController Edit</h1>'; }
    public function update(): void { echo '<h1>FineController Update</h1>'; }
    public function destroy(): void { echo '<h1>FineController Delete</h1>'; }
}