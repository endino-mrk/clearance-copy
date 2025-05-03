<?php
// Include helper functions
include_once BASE_PATH . '/includes/functions.php';

// Set page title
$pageTitle = 'System Settings';

// Sample data for system settings
$systemSettings = [
    'general' => [
        'system_name' => 'DormClear',
        'institution_name' => 'University Dormitory',
        'admin_email' => 'admin@dormclear.edu',
        'support_email' => 'support@dormclear.edu',
        'support_phone' => '+1 (555) 123-4567',
        'date_format' => 'M d, Y',
        'time_zone' => 'America/New_York'
    ],
    'notifications' => [
        'enable_email_notifications' => true,
        'enable_sms_notifications' => false,
        'payment_reminder_days' => 7,
        'document_reminder_days' => 10,
        'clearance_reminder_days' => 14,
        'enable_admin_notifications' => true
    ],
    'clearance' => [
        'auto_approve_clearance' => false,
        'require_room_inspection' => true,
        'require_financial_clearance' => true,
        'require_document_submission' => true,
        'clearance_expiry_days' => 30
    ],
    'payments' => [
        'allow_partial_payments' => true,
        'minimum_partial_payment' => 50,
        'late_payment_fee' => 25,
        'late_payment_days' => 5,
        'payment_methods' => [
            'credit_card' => true,
            'bank_transfer' => true,
            'cash' => true,
            'check' => false,
            'mobile_payment' => false
        ]
    ],
    'security' => [
        'password_expiry_days' => 90,
        'failed_login_attempts' => 5,
        'lockout_duration_minutes' => 30,
        'session_timeout_minutes' => 60,
        'require_two_factor' => false
    ]
];

// Sample user roles
$userRoles = [
    [
        'id' => 'ROLE-001',
        'name' => 'System Administrator',
        'description' => 'Full access to all system functions and settings',
        'permissions' => 'All',
        'users_count' => 2
    ],
    [
        'id' => 'ROLE-002',
        'name' => 'Dormitory Manager',
        'description' => 'Manages residents, rooms, and clearance processes',
        'permissions' => 'Manage residents, rooms, clearance, payments. View-only for settings.',
        'users_count' => 5
    ],
    [
        'id' => 'ROLE-003',
        'name' => 'Finance Officer',
        'description' => 'Manages payment records and financial transactions',
        'permissions' => 'Manage payments, fines. View-only for resident data.',
        'users_count' => 3
    ],
    [
        'id' => 'ROLE-004',
        'name' => 'Room Inspector',
        'description' => 'Conducts room inspections and maintenance checks',
        'permissions' => 'Manage room status, maintenance records. View-only for resident data.',
        'users_count' => 4
    ],
    [
        'id' => 'ROLE-005',
        'name' => 'Document Officer',
        'description' => 'Verifies document submissions from residents',
        'permissions' => 'Manage documents. View-only for resident data.',
        'users_count' => 2
    ]
];

// Sample users
$users = [
    [
        'id' => 'USER-001',
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'role' => 'System Administrator',
        'status' => 'Active',
        'last_login' => '2025-04-30 09:15:22'
    ],
    [
        'id' => 'USER-002',
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'role' => 'Dormitory Manager',
        'status' => 'Active',
        'last_login' => '2025-04-29 14:45:37'
    ],
    [
        'id' => 'USER-003',
        'name' => 'Michael Brown',
        'email' => 'michael.brown@example.com',
        'role' => 'Finance Officer',
        'status' => 'Active',
        'last_login' => '2025-04-28 11:22:18'
    ],
    [
        'id' => 'USER-004',
        'name' => 'Sarah Johnson',
        'email' => 'sarah.johnson@example.com',
        'role' => 'Room Inspector',
        'status' => 'Active',
        'last_login' => '2025-04-30 08:10:45'
    ],
    [
        'id' => 'USER-005',
        'name' => 'Robert Williams',
        'email' => 'robert.williams@example.com',
        'role' => 'Document Officer',
        'status' => 'Inactive',
        'last_login' => '2025-04-15 16:32:09'
    ]
];

// Include header
include BASE_PATH . '/includes/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Configure system preferences and manage user accounts</p>
    </div>
    <div class="flex space-x-2">
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-save-line"></i>
            <span>Save Changes</span>
        </button>
    </div>
</div>

<!-- Settings Tabs -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px" aria-label="Tabs">
            <button class="tab-button active border-transparent text-primary hover:text-primary-dark hover:border-primary-light whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="general">
                General Settings
            </button>
            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="users">
                User Management
            </button>
            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="notifications">
                Notifications
            </button>
            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="clearance">
                Clearance Settings
            </button>
            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="payments">
                Payment Settings
            </button>
            <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm" data-tab="security">
                Security Settings
            </button>
        </nav>
    </div>
    
    <!-- General Settings Tab -->
    <div class="tab-content p-6" id="general-tab">
        <h3 class="text-lg font-medium text-gray-900 mb-4">General System Settings</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <label for="system_name" class="block text-sm font-medium text-gray-700">System Name</label>
                    <input type="text" id="system_name" name="system_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['general']['system_name']; ?>">
                </div>
                <div class="mb-4">
                    <label for="institution_name" class="block text-sm font-medium text-gray-700">Institution Name</label>
                    <input type="text" id="institution_name" name="institution_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['general']['institution_name']; ?>">
                </div>
                <div class="mb-4">
                    <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                    <input type="email" id="admin_email" name="admin_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['general']['admin_email']; ?>">
                </div>
                <div class="mb-4">
                    <label for="support_email" class="block text-sm font-medium text-gray-700">Support Email</label>
                    <input type="email" id="support_email" name="support_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['general']['support_email']; ?>">
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label for="support_phone" class="block text-sm font-medium text-gray-700">Support Phone</label>
                    <input type="text" id="support_phone" name="support_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['general']['support_phone']; ?>">
                </div>
                <div class="mb-4">
                    <label for="date_format" class="block text-sm font-medium text-gray-700">Date Format</label>
                    <select id="date_format" name="date_format" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="M d, Y" <?php echo $systemSettings['general']['date_format'] == 'M d, Y' ? 'selected' : ''; ?>>Jan 01, 2025</option>
                        <option value="d/m/Y" <?php echo $systemSettings['general']['date_format'] == 'd/m/Y' ? 'selected' : ''; ?>>01/01/2025</option>
                        <option value="m/d/Y" <?php echo $systemSettings['general']['date_format'] == 'm/d/Y' ? 'selected' : ''; ?>>01/01/2025</option>
                        <option value="Y-m-d" <?php echo $systemSettings['general']['date_format'] == 'Y-m-d' ? 'selected' : ''; ?>>2025-01-01</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="time_zone" class="block text-sm font-medium text-gray-700">Time Zone</label>
                    <select id="time_zone" name="time_zone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        <option value="America/New_York" <?php echo $systemSettings['general']['time_zone'] == 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (US & Canada)</option>
                        <option value="America/Chicago" <?php echo $systemSettings['general']['time_zone'] == 'America/Chicago' ? 'selected' : ''; ?>>Central Time (US & Canada)</option>
                        <option value="America/Denver" <?php echo $systemSettings['general']['time_zone'] == 'America/Denver' ? 'selected' : ''; ?>>Mountain Time (US & Canada)</option>
                        <option value="America/Los_Angeles" <?php echo $systemSettings['general']['time_zone'] == 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (US & Canada)</option>
                        <option value="UTC" <?php echo $systemSettings['general']['time_zone'] == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="system_logo" class="block text-sm font-medium text-gray-700">System Logo</label>
                    <div class="mt-1 flex items-center">
                        <span class="inline-block h-12 w-12 overflow-hidden rounded-full bg-gray-100">
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <button type="button" class="ml-5 rounded-md border border-gray-300 bg-white py-2 px-3 text-sm font-medium leading-4 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">Change</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Management Tab -->
    <div class="tab-content p-6 hidden" id="users-tab">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">User Management</h3>
            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <i class="ri-user-add-line mr-2"></i> Add New User
            </button>
        </div>
        
        <div class="mb-6">
            <h4 class="text-md font-medium text-gray-700 mb-2">User Roles</h4>
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($userRoles as $role): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $role['name']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $role['description']; ?></td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500"><?php echo $role['permissions']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $role['users_count']; ?> users</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-primary hover:text-primary-dark">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div>
            <div class="flex justify-between items-center mb-2">
                <h4 class="text-md font-medium text-gray-700">User Accounts</h4>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search users...">
                </div>
            </div>
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                            <span class="font-medium"><?php echo getInitials($user['name']); ?></span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $user['name']; ?></div>
                                            <div class="text-sm text-gray-500"><?php echo $user['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['email']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['role']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['status'] === 'Active'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['last_login']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2 justify-end">
                                        <button class="text-blue-600 hover:text-blue-900" title="Edit User">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Deactivate User">
                                            <i class="ri-user-unfollow-line"></i>
                                        </button>
                                        <button class="text-primary hover:text-primary-dark" title="Reset Password">
                                            <i class="ri-lock-password-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Notifications Tab -->
    <div class="tab-content p-6 hidden" id="notifications-tab">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="enable_email" class="block text-sm font-medium text-gray-700">Enable Email Notifications</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="enable_email" name="enable_email" <?php echo $systemSettings['notifications']['enable_email_notifications'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Send notifications to residents and administrators via email</p>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="enable_sms" class="block text-sm font-medium text-gray-700">Enable SMS Notifications</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="enable_sms" name="enable_sms" <?php echo $systemSettings['notifications']['enable_sms_notifications'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Send notifications to residents and administrators via SMS</p>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="enable_admin_notifications" class="block text-sm font-medium text-gray-700">Admin Notifications</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="enable_admin_notifications" name="enable_admin_notifications" <?php echo $systemSettings['notifications']['enable_admin_notifications'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Send system notifications to administrators</p>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label for="payment_reminder_days" class="block text-sm font-medium text-gray-700">Payment Reminder Days</label>
                    <input type="number" id="payment_reminder_days" name="payment_reminder_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['notifications']['payment_reminder_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Days before due date to send payment reminders</p>
                </div>
                <div class="mb-4">
                    <label for="document_reminder_days" class="block text-sm font-medium text-gray-700">Document Reminder Days</label>
                    <input type="number" id="document_reminder_days" name="document_reminder_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['notifications']['document_reminder_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Days before due date to send document submission reminders</p>
                </div>
                <div class="mb-4">
                    <label for="clearance_reminder_days" class="block text-sm font-medium text-gray-700">Clearance Reminder Days</label>
                    <input type="number" id="clearance_reminder_days" name="clearance_reminder_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['notifications']['clearance_reminder_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Days before move-out date to send clearance reminders</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Clearance Settings Tab -->
    <div class="tab-content p-6 hidden" id="clearance-tab">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Clearance Process Settings</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="auto_approve_clearance" class="block text-sm font-medium text-gray-700">Auto-Approve Clearance</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="auto_approve_clearance" name="auto_approve_clearance" <?php echo $systemSettings['clearance']['auto_approve_clearance'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Automatically approve clearance when all requirements are met</p>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="require_room_inspection" class="block text-sm font-medium text-gray-700">Require Room Inspection</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="require_room_inspection" name="require_room_inspection" <?php echo $systemSettings['clearance']['require_room_inspection'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Require room inspection for clearance approval</p>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="require_financial_clearance" class="block text-sm font-medium text-gray-700">Require Financial Clearance</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="require_financial_clearance" name="require_financial_clearance" <?php echo $systemSettings['clearance']['require_financial_clearance'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Require settlement of all financial obligations for clearance</p>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="require_document_submission" class="block text-sm font-medium text-gray-700">Require Document Submission</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="require_document_submission" name="require_document_submission" <?php echo $systemSettings['clearance']['require_document_submission'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Require submission of all required documents for clearance</p>
                </div>
                <div class="mb-4">
                    <label for="clearance_expiry_days" class="block text-sm font-medium text-gray-700">Clearance Expiry Days</label>
                    <input type="number" id="clearance_expiry_days" name="clearance_expiry_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['clearance']['clearance_expiry_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Number of days after which an unused clearance expires</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Settings Tab -->
    <div class="tab-content p-6 hidden" id="payments-tab">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Settings</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="allow_partial_payments" class="block text-sm font-medium text-gray-700">Allow Partial Payments</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="allow_partial_payments" name="allow_partial_payments" <?php echo $systemSettings['payments']['allow_partial_payments'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Allow residents to make partial payments for fees</p>
                </div>
                <div class="mb-4">
                    <label for="minimum_partial_payment" class="block text-sm font-medium text-gray-700">Minimum Partial Payment (%)</label>
                    <input type="number" id="minimum_partial_payment" name="minimum_partial_payment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['payments']['minimum_partial_payment']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Minimum percentage of total amount for partial payments</p>
                </div>
                <div class="mb-4">
                    <label for="late_payment_fee" class="block text-sm font-medium text-gray-700">Late Payment Fee ($)</label>
                    <input type="number" id="late_payment_fee" name="late_payment_fee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['payments']['late_payment_fee']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Fee amount for late payments</p>
                </div>
                <div class="mb-4">
                    <label for="late_payment_days" class="block text-sm font-medium text-gray-700">Late Payment Days</label>
                    <input type="number" id="late_payment_days" name="late_payment_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['payments']['late_payment_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Days after due date when late payment fee applies</p>
                </div>
            </div>
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-2">Accepted Payment Methods</h4>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="credit_card" name="credit_card" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo $systemSettings['payments']['payment_methods']['credit_card'] ? 'checked' : ''; ?>>
                        <label for="credit_card" class="ml-2 block text-sm text-gray-700">Credit/Debit Card</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="bank_transfer" name="bank_transfer" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo $systemSettings['payments']['payment_methods']['bank_transfer'] ? 'checked' : ''; ?>>
                        <label for="bank_transfer" class="ml-2 block text-sm text-gray-700">Bank Transfer</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="cash" name="cash" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo $systemSettings['payments']['payment_methods']['cash'] ? 'checked' : ''; ?>>
                        <label for="cash" class="ml-2 block text-sm text-gray-700">Cash</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="check" name="check" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo $systemSettings['payments']['payment_methods']['check'] ? 'checked' : ''; ?>>
                        <label for="check" class="ml-2 block text-sm text-gray-700">Check</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="mobile_payment" name="mobile_payment" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo $systemSettings['payments']['payment_methods']['mobile_payment'] ? 'checked' : ''; ?>>
                        <label for="mobile_payment" class="ml-2 block text-sm text-gray-700">Mobile Payment</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Security Settings Tab -->
    <div class="tab-content p-6 hidden" id="security-tab">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Security Settings</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <div class="mb-4">
                    <label for="password_expiry_days" class="block text-sm font-medium text-gray-700">Password Expiry Days</label>
                    <input type="number" id="password_expiry_days" name="password_expiry_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['security']['password_expiry_days']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Number of days after which user passwords expire</p>
                </div>
                <div class="mb-4">
                    <label for="failed_login_attempts" class="block text-sm font-medium text-gray-700">Failed Login Attempts</label>
                    <input type="number" id="failed_login_attempts" name="failed_login_attempts" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['security']['failed_login_attempts']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Number of failed login attempts before account lockout</p>
                </div>
                <div class="mb-4">
                    <label for="lockout_duration_minutes" class="block text-sm font-medium text-gray-700">Lockout Duration (minutes)</label>
                    <input type="number" id="lockout_duration_minutes" name="lockout_duration_minutes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['security']['lockout_duration_minutes']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Duration of account lockout after failed login attempts</p>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label for="session_timeout_minutes" class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                    <input type="number" id="session_timeout_minutes" name="session_timeout_minutes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" value="<?php echo $systemSettings['security']['session_timeout_minutes']; ?>">
                    <p class="mt-1 text-sm text-gray-500">Duration of user inactivity before automatic logout</p>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <label for="require_two_factor" class="block text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                        <label class="custom-switch">
                            <input type="checkbox" id="require_two_factor" name="require_two_factor" <?php echo $systemSettings['security']['require_two_factor'] ? 'checked' : ''; ?>>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Require two-factor authentication for all users</p>
                </div>
                <div class="mt-6">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="ri-refresh-line mr-2"></i> Reset All Security Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching Logic
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Deactivate all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('text-primary', 'border-primary');
                btn.classList.add('text-gray-500', 'border-transparent');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Activate the clicked tab
            this.classList.add('active', 'text-primary', 'border-primary');
            this.classList.remove('text-gray-500', 'border-transparent');
            
            // Show the corresponding tab content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-tab').classList.remove('hidden');
        });
    });
});
</script>

<?php
// Include footer
include BASE_PATH . '/includes/footer.php';
?>