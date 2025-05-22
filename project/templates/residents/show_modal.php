<!-- View Resident Modal -->
<div id="viewResidentModal" class="fixed inset-0 z-50 overflow-auto flex items-center justify-center hidden">
    <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 md:mx-auto transform transition-all duration-300 ease-in-out opacity-0 translate-y-4 scale-95">
        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">
                Resident Details
            </h3>
            <button id="closeViewModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <!-- Resident Details -->
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="h-20 w-20 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary text-xl font-bold">
                    <span id="view_initials">JD</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold">
                        <span id="view_first_name">John</span> <span id="view_last_name">Doe</span>
                    </h2>
                    <p class="text-gray-500">Resident Account</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex border-b border-gray-200 py-3">
                    <div class="w-1/3 text-gray-500">Middle Name</div>
                    <div id="view_middle_name" class="w-2/3 font-medium">-</div>
                </div>
                
                <div class="flex border-b border-gray-200 py-3">
                    <div class="w-1/3 text-gray-500">Email</div>
                    <div id="view_email" class="w-2/3 font-medium">john.doe@example.com</div>
                </div>
                
                <div class="flex border-b border-gray-200 py-3">
                    <div class="w-1/3 text-gray-500">Phone Number</div>
                    <div id="view_phone_number" class="w-2/3 font-medium">-</div>
                </div>
                
                <div class="flex border-b border-gray-200 py-3">
                    <div class="w-1/3 text-gray-500">Created Date</div>
                    <div id="view_created_at" class="w-2/3 font-medium">Jan 1, 2023</div>
                </div>
            </div>
        </div>
        
        <!-- Actions Footer -->
        <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b">
            <button id="closeViewBtn" type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md">
                Close
            </button>
        </div>
    </div>
</div> 