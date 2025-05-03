<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dormitory Clearance System</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config={
            theme:{
                extend:{
                    colors:{
                        primary:'#4f46e5',
                        secondary:'#6366f1'
                    },
                    borderRadius:{
                        'none':'0px',
                        'sm':'4px',
                        DEFAULT:'8px',
                        'md':'12px',
                        'lg':'16px',
                        'xl':'20px',
                        '2xl':'24px',
                        '3xl':'32px',
                        'full':'9999px',
                        'button':'8px'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-10 px-8">
            <div class="flex justify-center mb-8">
                <h1 class="text-4xl font-['Pacifico'] text-primary">DormClear</h1>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Create an account</h2>
            <p class="text-gray-600 text-center mb-8">Register to access the dormitory clearance system</p>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">
                    <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/register" method="POST">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-gray-700 text-sm font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required
                            class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                            placeholder="First Name"
                            value="<?php echo isset($_SESSION['old_input']['first_name']) ? htmlspecialchars($_SESSION['old_input']['first_name']) : ''; ?>">
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 text-sm font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                            class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                            placeholder="Last Name"
                            value="<?php echo isset($_SESSION['old_input']['last_name']) ? htmlspecialchars($_SESSION['old_input']['last_name']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="middle_name" class="block text-gray-700 text-sm font-medium mb-2">Middle Name (Optional)</label>
                    <input type="text" id="middle_name" name="middle_name"
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Middle Name"
                        value="<?php echo isset($_SESSION['old_input']['middle_name']) ? htmlspecialchars($_SESSION['old_input']['middle_name']) : ''; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="example@email.com"
                        value="<?php echo isset($_SESSION['old_input']['email']) ? htmlspecialchars($_SESSION['old_input']['email']) : ''; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="phone_number" class="block text-gray-700 text-sm font-medium mb-2">Phone Number (Optional)</label>
                    <input type="tel" id="phone_number" name="phone_number"
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Enter your phone number"
                        value="<?php echo isset($_SESSION['old_input']['phone_number']) ? htmlspecialchars($_SESSION['old_input']['phone_number']) : ''; ?>">
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Choose a password">
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters</p>
                </div>
                
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="shadow-sm block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Confirm your password">
                </div>
                
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="terms" name="terms" required
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-primary hover:underline">Terms and Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition">
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">Already have an account? 
                    <a href="/login" class="text-primary hover:underline">Login here</a>
                </p>
            </div>
            <?php if (isset($_SESSION['old_input'])) unset($_SESSION['old_input']); ?>
        </div>
    </div>
</body>
</html> 