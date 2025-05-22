<!-- Modal Form for Recording Payment -->
<div id="recordPaymentModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Record Payment</h2>
            <button id="closeModal" class="text-gray-500">
                <i class="ri-close-line ri-lg"></i>
            </button>
        </div>
        <form  method="POST">
            <!-- Student ID -->
            <div class="mt-4">
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                <input type="text" id="student_id" name="student_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <!-- Amount Paid -->
            <div class="mt-4">
                <label for="amount_paid" class="block text-sm font-medium text-gray-700">Amount Paid</label>
                <input type="number" id="amount_paid" name="amount_paid" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <!-- Receipt Number -->
            <div class="mt-4">
                <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
                <input type="text" id="receipt_number" name="receipt_number" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <!-- Payment Date -->
            <div class="mb-4">
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" 
                                    value="<?php echo isset($_SESSION['old']['payment_date']) ? $_SESSION['old']['violation_date'] : date('Y-m-d'); ?>"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                    required>
                                <?php if (isset($_SESSION['errors']['payment_date'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['violation_date']; ?></p>
                                <?php endif; ?>
                            </div>
            <!-- Buttons -->
            <div class="mt-4 flex justify-center">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md">Submit Payment</button>
            </div>
        </form>
            </div>
    </div>