// Add this code to fine-list.php just before the closing body tag (after including payment.php)

<!-- Modal for editing fine - styled to match the Issue New Fine modal -->
<div id="editFineModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 pt-5 pb-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-medium text-gray-900">Edit Fine</h3>
                    <button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                
                <form id="editFineForm" action="functions/edit-functions.php" method="POST">
                    <input type="hidden" name="fine_id" id="edit_fine_id">
                    
                    <!-- Form fields styled to match the Issue New Fine modal -->
                    <div class="space-y-4">
                        <!-- Resident Information Display -->
                        <div id="editResidentInfo" class="mb-2 px-1"></div>
                        
                        <!-- Student ID (Editable) -->
                        <div>
                            <label for="edit_student_id" class="block text-sm font-medium text-gray-700 mb-1">Resident (Student ID)</label>
                            <input type="text" id="edit_student_id" name="student_id" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Enter student ID">
                        </div>
                        
                        <!-- Fine Type (Editable) -->
                        <div>
                            <label for="edit_fine_type_id" class="block text-sm font-medium text-gray-700 mb-1">Fine Type</label>
                            <select name="fine_id" id="edit_fine_type_id" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">Select Fine Type</option>
                                <?php foreach ($fineTypes as $fineType): ?>
                                    <option value="<?php echo $fineType['fine_id']; ?>" 
                                            data-amount="<?php echo $fineType['amount']; ?>" 
                                            data-description="<?php echo htmlspecialchars($fineType['description']); ?>">
                                        <?php echo $fineType['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="editFineDetails" class="mt-2 text-sm text-gray-500 hidden p-2 bg-gray-50 rounded-md">
                                <p><span class="font-medium">Amount:</span> ₱<span id="editFineAmount"></span></p>
                                <p><span class="font-medium">Description:</span> <span id="editFineDescription"></span></p>
                            </div>
                        </div>

                        <!-- Violation Date (Editable) -->
                        <div>
                            <label for="edit_violation_date" class="block text-sm font-medium text-gray-700 mb-1">Violation Date</label>
                            <input type="date" name="violation_date" id="edit_violation_date" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   required>
                        </div>

                        <!-- Additional Description (Optional) -->
                        <div id="editDescriptionContainer">
                            <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                            <textarea name="description" id="edit_description" rows="3"
                                     class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    
                    <!-- Button group aligned to the right -->
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="cancelEditFine" class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Fine
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>