<?php

namespace App\Controllers;

class PaymentController extends BaseController
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
        
        // Render the payment history view
        $this->view('payment-history/index');
    }
    
    public function create(): void { echo '<h1>PaymentController Create</h1>'; }
    public function store(): void { echo '<h1>PaymentController Store</h1>'; }
    public function show(): void { echo '<h1>PaymentController Show</h1>'; }
    public function edit(): void { echo '<h1>PaymentController Edit</h1>'; }
    public function update(): void { echo '<h1>PaymentController Update</h1>'; }
    public function destroy(): void { echo '<h1>PaymentController Delete</h1>'; }
}