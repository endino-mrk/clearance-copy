<?php
// Include helper functions if not already included
if (!function_exists('isPageActive')) {
    include_once BASE_PATH . '/app/helpers.php';
}
?>

<!-- Sidebar -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 border-r border-gray-200 bg-white">
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <h1 class="text-xl font-['Pacifico'] text-primary">DormClear</h1>
        </div>
        <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</p>
                <a href="/admin" class="sidebar-link <?php echo isPageActive('index.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    Overview
                </a>
            </div>
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Residents</p>
                <a href="/residents-account" class="sidebar-link <?php echo isPageActive('manage_residents.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-user-line"></i>
                    </div>
                    Residents Account
                </a>
                <a href="/residents" class="sidebar-link <?php echo isPageActive('manage_residents.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-user-line"></i>
                    </div>
                    Manage Residents
                </a>
                <a href="/clearance" class="sidebar-link <?php echo isPageActive('clearance_status.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-check-double-line"></i>
                    </div>
                    Clearance Status
                </a>
            </div>
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                <a href="/rental-fees" class="sidebar-link <?php echo isPageActive('rental_fees.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    Rental Fees
                </a>
                <a href="/fines" class="sidebar-link <?php echo isPageActive('fines.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-bill-line"></i>
                    </div>
                    Fines
                </a>
                <a href="/payments" class="sidebar-link <?php echo isPageActive('payment_history.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-history-line"></i>
                    </div>
                    Payment History
                </a>
            </div>
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Rooms</p>
                <a href="/rooms" class="sidebar-link <?php echo isPageActive('room_status.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-home-line"></i>
                    </div>
                    Room Status
                </a>
            </div>
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Documents</p>
                <a href="/documents" class="sidebar-link <?php echo isPageActive('document_tracker.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-file-list-line"></i>
                    </div>
                    Document Tracker
                </a>
            </div>
            <div class="mt-auto">
                <!-- <a href="/settings" class="sidebar-link <?php echo isPageActive('settings.php') ? 'active' : ''; ?> flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-settings-line"></i>
                    </div>
                    Settings
                </a> -->
                <form action="/logout" method="POST" class="m-0">
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