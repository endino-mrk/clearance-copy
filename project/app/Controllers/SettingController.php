<?php

namespace App\Controllers;

class SettingController extends BaseController
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
        
        // Render the settings view
        $this->view('settings/index');
    }
    
    public function create(): void { echo '<h1>SettingController Create</h1>'; }
    public function store(): void { echo '<h1>SettingController Store</h1>'; }
    public function show(): void { echo '<h1>SettingController Show</h1>'; }
    public function edit(): void { echo '<h1>SettingController Edit</h1>'; }
    public function update(): void { echo '<h1>SettingController Update</h1>'; }
    public function destroy(): void { echo '<h1>SettingController Delete</h1>'; }
}