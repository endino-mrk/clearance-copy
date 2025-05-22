<!-- Delete Resident Modal -->
<div id="deleteResidentModal" class="fixed inset-0 z-50 overflow-auto flex items-center justify-center hidden">
    <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 md:mx-auto transform transition-all duration-300 ease-in-out opacity-0 translate-y-4 scale-95">
        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">
                Delete Resident
            </h3>
            <button id="closeDeleteModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <!-- Delete Confirmation -->
        <div class="p-6">
            <form id="deleteResidentForm" method="POST">
                <input type="hidden" id="delete_resident_id" name="id" value="">
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="ri-delete-bin-line text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Delete Resident Account</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete the resident account for <span id="delete_resident_name" class="font-semibold"></span>? This action cannot be undone.
                        </p>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="cancelDeleteBtn" type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Cancel
                    </button>
                    <button id="deleteSubmitBtn" type="submit" class="inline-flex justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
