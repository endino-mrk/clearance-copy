<?php

namespace App\Controllers;

class RentalFeeController extends BaseController
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
        
        // Render the rental fees view
        $this->view('rental-fees/index');
    }
    
    public function create(): void { echo '<h1>RentalFeeController Create</h1>'; }
    public function store(): void { echo '<h1>RentalFeeController Store</h1>'; }
    public function show(): void { echo '<h1>RentalFeeController Show</h1>'; }
    public function edit(): void { echo '<h1>RentalFeeController Edit</h1>'; }
    public function update(): void { echo '<h1>RentalFeeController Update</h1>'; }
    public function destroy(): void { echo '<h1>RentalFeeController Delete</h1>'; }
}