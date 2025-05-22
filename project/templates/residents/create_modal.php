<!-- Create Resident Modal -->
<div id="createResidentModal" class="fixed inset-0 z-50 overflow-auto flex items-center justify-center hidden">
    <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 md:mx-auto transform transition-all duration-300 ease-in-out opacity-0 translate-y-4 scale-95">
        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">
                Add New Resident
            </h3>
            <button id="closeCreateModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form id="createResidentForm" method="POST" action="/residents-account" class="p-6">
            <!-- First & Last Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo old('first_name'); ?>" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <?php if (hasError('first_name')): ?>
                        <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('first_name'); ?></p>
                    <?php else: ?>
                        <p class="text-red-500 text-xs mt-1 error-message hidden" id="first_name_error"></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo old('last_name'); ?>" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <?php if (hasError('last_name')): ?>
                        <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('last_name'); ?></p>
                    <?php else: ?>
                        <p class="text-red-500 text-xs mt-1 error-message hidden" id="last_name_error"></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Middle Name -->
            <div class="mb-4">
                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name (Optional)</label>
                <input type="text" name="middle_name" id="middle_name" value="<?php echo old('middle_name'); ?>" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <?php if (hasError('middle_name')): ?>
                    <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('middle_name'); ?></p>
                <?php else: ?>
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="middle_name_error"></p>
                <?php endif; ?>
            </div>
            
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="<?php echo old('email'); ?>" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                       required>
                <?php if (hasError('email')): ?>
                    <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('email'); ?></p>
                <?php else: ?>
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="email_error"></p>
                <?php endif; ?>
            </div>
            
            <!-- Phone -->
            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number (Optional)</label>
                <input type="tel" name="phone_number" id="phone_number" value="<?php echo old('phone_number'); ?>" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <?php if (hasError('phone_number')): ?>
                    <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('phone_number'); ?></p>
                <?php else: ?>
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="phone_number_error"></p>
                <?php endif; ?>
            </div>
            
            <!-- Password Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <?php if (hasError('password')): ?>
                        <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('password'); ?></p>
                    <?php else: ?>
                        <p class="text-red-500 text-xs mt-1 error-message hidden" id="password_error"></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           required>
                    <?php if (hasError('password_confirmation')): ?>
                        <p class="text-red-500 text-xs mt-1 error-message"><?php echo getError('password_confirmation'); ?></p>
                    <?php else: ?>
                        <p class="text-red-500 text-xs mt-1 error-message hidden" id="password_confirmation_error"></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b">
                <button id="cancelCreateBtn" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Cancel
                </button>
                <button id="createSubmitBtn" type="submit" class="ml-4 inline-flex justify-center px-4 py-2 bg-primary border border-transparent rounded-md font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Create Resident
                </button>
            </div>
        </form>
    </div>
</div>
