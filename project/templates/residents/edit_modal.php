<!-- Edit Resident Modal -->
<div id="editResidentModal" class="fixed inset-0 z-50 overflow-auto flex items-center justify-center hidden">
    <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 md:mx-auto transform transition-all duration-300 ease-in-out opacity-0 translate-y-4 scale-95">
        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">
                Edit Resident
            </h3>
            <button id="closeEditModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form id="editResidentForm" method="POST" class="p-6">
            <input type="hidden" id="edit_resident_id" name="id" value="">
            
            <!-- First & Last Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="edit_first_name" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_first_name_error"></p>
                </div>
                <div>
                    <label for="edit_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="edit_last_name" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_last_name_error"></p>
                </div>
            </div>
            
            <!-- Middle Name -->
            <div class="mb-4">
                <label for="edit_middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name (Optional)</label>
                <input type="text" name="middle_name" id="edit_middle_name" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_middle_name_error"></p>
            </div>
            
            <!-- Email -->
            <div class="mb-4">
                <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="edit_email" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                       required>
                <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_email_error"></p>
            </div>
            
            <!-- Phone -->
            <div class="mb-4">
                <label for="edit_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                <input type="tel" name="phone_number" id="edit_phone_number" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_phone_number_error"></p>
            </div>
            
            <!-- Password Fields (Optional during edit) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border-t border-gray-200 pt-4 mt-4">
                <div class="col-span-2 mb-2">
                    <p class="text-sm text-gray-500">Leave password fields empty to keep current password</p>
                </div>
                <div>
                    <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" id="edit_password" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_password_error"></p>
                </div>
                <div>
                    <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="edit_password_confirmation" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="edit_password_confirmation_error"></p>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b">
                <button id="cancelEditBtn" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Cancel
                </button>
                <button id="editSubmitBtn" type="submit" class="ml-4 inline-flex justify-center px-4 py-2 bg-primary border border-transparent rounded-md font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div> 