<?php
// Set default page title if not already set
if (!isset($pageTitle)) {
    $pageTitle = 'Dormitory Clearance System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Dormitory Clearance System</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <?php if (isset($useCharts) && $useCharts): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<div class="flex h-screen bg-gray-50">
    <!-- Include sidebar -->
    <?php include BASE_PATH . '/app/Views/components/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <!-- Top Navigation -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-white">
            <div class="flex items-center md:hidden">
                <button class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600" id="sidebarToggle">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-menu-line"></i>
                    </div>
                </button>
            </div>
            <div class="md:hidden flex items-center">
                <h1 class="text-xl font-['Pacifico'] text-primary">DormClear</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="p-1 text-gray-400 rounded-full hover:bg-gray-100 focus:outline-none">
                        <div class="w-6 h-6 flex items-center justify-center">
                            <i class="ri-notification-3-line"></i>
                        </div>
                    </button>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </div>
                <div class="relative">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-medium">
                            JD
                        </div>
                        <span class="hidden md:block text-sm font-medium">John Doe</span>
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4">
            <div class="max-w-7xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="mb-4 text-sm" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-1">
                        <li>
                            <a href="index.php" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                        </li>
                        <li class="flex items-center">
                            <div class="w-4 h-4 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                            <span class="text-gray-700 font-medium"><?php echo $pageTitle; ?></span>
                        </li>
                    </ol>
                </nav> 