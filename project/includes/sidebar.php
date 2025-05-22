<?php
// Helper function included via bootstrap.php now
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/role_auth_check.php';

// Function to check if the current script matches the link path
if (!function_exists('isPageActive')) {
    function isPageActive($path) {
        // Get the current script name relative to the public directory
        $currentScript = ltrim($_SERVER['SCRIPT_NAME'], '/'); // e.g., residents/index.php
        $linkPath = ltrim($path, '/'); // e.g., residents/index.php

        // Special case for root index.php
        if ($currentScript === 'index.php' && $linkPath === '') {
            return true;
        }

        return $currentScript === $linkPath;
    }
}
?>

<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 border-r border-gray-200 bg-white">
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <h1 class="text-xl font-['Pacifico'] text-primary">DormClear</h1>
        </div>
        <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
            
            <?php if (isManager()): ?>
            <!-- Manager Navigation -->
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</p>
                <a href="/clearance/project/public/index.php" class="sidebar-link <?php echo isPageActive('index.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    Overview
                </a>
            </div>

            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Residents</p>
                <a href="/clearance/project/public/role-admin/residents-accounts/resident-account-list.php" class="sidebar-link <?php echo isPageActive('resident-account-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-user-line"></i>
                    </div>
                    Residents Account
                </a>
               
                <a href="/clearance/project/public/role-admin/clearance-status/clearance-status-list.php" class="sidebar-link <?php echo isPageActive('clearance-status-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-check-double-line"></i>
                    </div>
                    Clearance Status
                </a>
            </div>
         
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                <a href="/clearance/project/public/role-admin/rental-fees/rental-fee-list.php" class="sidebar-link <?php echo isPageActive('rental-fee-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    Rental Fees
                </a>

                <a href="/clearance/project/public/role-admin/fines/fine-list.php" class="sidebar-link <?php echo isPageActive('fine-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-bill-line"></i>
                    </div>
                    Fines
                </a>

                <a href="/clearance/project/public/role-admin/payment-history/payment-history-list.php" class="sidebar-link <?php echo isPageActive('payment-history-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-history-line"></i>
                    </div>
                    Payment History
                </a>
            </div>
 
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Room</p>
                <a href="/clearance/project/public/role-admin/room-status/room-status-list.php" class="sidebar-link <?php echo isPageActive('room-status-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                        <div class="w-5 h-5 mr-3 flex items-center justify-center">
                            <i class="ri-home-line"></i>
                        </div>
                    Room Status
                </a>
            </div>
 
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Documents</p>
                <a href="/clearance/project/public/role-admin/documents/document-list.php" class="sidebar-link <?php echo isPageActive('document_tracker.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-file-list-line"></i>
                    </div> 
                    Document Tracker
                </a>
            </div>
  
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Semester</p>
                <a href="/clearance/project/public/role-admin/semesters/semester-management.php" class="sidebar-link <?php echo isPageActive('semester-management.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-file-list-line"></i>
                    </div> 
                    Manage Semesters
                </a>
            </div>
            
            <?php elseif (isTreasurer()): ?>
            <!-- Treasurer Navigation -->
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</p>
                <a href="/clearance/project/public/role-admin/clearance-status/resident-clearance.php" class="sidebar-link <?php echo isPageActive('resident-clearance.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    My Clearance
                </a>
            </div>

            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                <a href="/clearance/project/public/role-admin/fines/fine-list.php" class="sidebar-link <?php echo isPageActive('fine-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-bill-line"></i>
                    </div>
                    Manage Fines
                </a>

                <a href="/clearance/project/public/role-admin/payment-history/payment-history-list.php" class="sidebar-link <?php echo isPageActive('payment-history-list.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-history-line"></i>
                    </div>
                    Payment History
                </a>
            </div>
            
            <?php else: ?>
            <!-- Resident Navigation (default) -->
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</p>
                <a href="/clearance/project/public/role-admin/clearance-status/resident-clearance.php" class="sidebar-link <?php echo isPageActive('resident-clearance.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    My Clearance
                </a>
            </div>
            <?php endif; ?>

            <div class="mt-auto">
                <form action="/clearance/project/public/logout.php" method="POST" class="m-0">
                    <button type="submit" class="w-full sidebar-link flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                        <div class="w-5 h-5 mr-3 flex items-center justify-center">
                            <i class="ri-logout-box-line"></i>
                        </div>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>