<?php

namespace App\Controllers;

class DocumentController extends BaseController
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
        
        // Render the document tracker view
        $this->view('document-tracker/index');
    }
    
    public function create(): void { echo '<h1>DocumentController Create</h1>'; }
    public function store(): void { echo '<h1>DocumentController Store</h1>'; }
    public function show(): void { echo '<h1>DocumentController Show</h1>'; }
    public function edit(): void { echo '<h1>DocumentController Edit</h1>'; }
    public function update(): void { echo '<h1>DocumentController Update</h1>'; }
    public function destroy(): void { echo '<h1>DocumentController Delete</h1>'; }
}