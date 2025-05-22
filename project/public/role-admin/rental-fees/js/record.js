// Get elements
    const recordPaymentButton = document.querySelector("button.bg-primary");
    const recordPaymentModal = document.getElementById("recordPaymentModal");
    const closeModalButton = document.getElementById("closeModal");
    const cancelModalButton = document.getElementById("cancelModal");

    // Show modal on button click
    recordPaymentButton.addEventListener("click", function() {
        recordPaymentModal.classList.remove("hidden");
    });

    // Close modal
    closeModalButton.addEventListener("click", function() {
        recordPaymentModal.classList.add("hidden");
    });

    // Cancel and close modal
    cancelModalButton.addEventListener("click", function() {
        recordPaymentModal.classList.add("hidden");
    });

    // Close modal when clicked outside
    window.addEventListener("click", function(event) {
        if (event.target === recordPaymentModal) {
            recordPaymentModal.classList.add("hidden");
        }
    });