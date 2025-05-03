<?php

namespace App\Controllers;

class RoomController extends BaseController
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
        
        // Render the room status view
        $this->view('room-status/index');
    }
    
    public function create(): void { echo '<h1>RoomController Create</h1>'; }
    public function store(): void { echo '<h1>RoomController Store</h1>'; }
    public function show(): void { echo '<h1>RoomController Show</h1>'; }
    public function edit(): void { echo '<h1>RoomController Edit</h1>'; }
    public function update(): void { echo '<h1>RoomController Update</h1>'; }
    public function destroy(): void { echo '<h1>RoomController Delete</h1>'; }
} 