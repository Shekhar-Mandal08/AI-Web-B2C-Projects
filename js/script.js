/*
   NEPAL MILK DAIRY - Dynamic Scripts
   Functionality: Mobile Navigation, Basic Validation
*/

document.addEventListener('DOMContentLoaded', function () {

    // -------------------------------------------------------------------------
    // Mobile Navigation Toggle
    // -------------------------------------------------------------------------
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }

    // -------------------------------------------------------------------------
    // Form Validation (Simple Client-Side)
    // -------------------------------------------------------------------------
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        // Only attach if we want client-side validation blocking
        // We will do basic header check
        form.addEventListener('submit', function (e) {
            const requiredInputs = form.querySelectorAll('[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'var(--danger-color)';
                } else {
                    input.style.borderColor = 'var(--border-color)';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});
