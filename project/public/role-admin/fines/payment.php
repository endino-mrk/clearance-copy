
<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900">Record Fine Payment</h3>
                            <button type="button" id="closePaymentModal" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>
                        
                        <form id="paymentForm" action="functions/process-payment.php" method="POST">
                            <input type="hidden" id="payment_fine_id" name="fine_id">
                            
                            <!-- Fine Information Summary -->
                            <div class="mb-4 bg-gray-50 p-3 rounded-md">
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Total Amount:</span>
                                        <span id="payment_fine_amount" class="font-semibold text-gray-900 ml-1"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Already Paid:</span>
                                        <span id="payment_amount_paid" class="font-semibold text-gray-900 ml-1"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-500">Remaining Balance:</span>
                                        <span id="payment_balance" class="font-semibold text-primary ml-1"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Amount -->
                            <div class="mb-4">
                                <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Payment Amount</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">₱</span>
                                    </div>
                                    <input type="number" step="0.01" id="payment_amount" name="amount" 
                                           class="block w-full pl-7 px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="0.00" required min="0.01">
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Amount cannot exceed the remaining balance</div>
                                <?php if (isset($_SESSION['errors']['amount'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['amount']; ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Receipt Number -->
                            <div class="mb-4">
                                <label for="receipt_no" class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                                <input type="text" id="receipt_no" name="receipt_no" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                       placeholder="Enter receipt number" required>
                                <?php if (isset($_SESSION['errors']['receipt_no'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['receipt_no']; ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Payment Date -->
                            <div class="mb-6">
                                <label for="date_paid" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                <input type="date" id="date_paid" name="date_paid" 
                                       value="<?php echo date('Y-m-d'); ?>"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                       required>
                                <?php if (isset($_SESSION['errors']['date_paid'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['date_paid']; ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Button group -->
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Record Payment
                                </button>
                                <button type="button" id="cancelPayment" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>