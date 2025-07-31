// Select the form and its fields
const form = document.querySelector('form');
const emailField = document.getElementById('email');
const passwordField = document.getElementById('password');

// Add an event listener for the form submission
form.addEventListener('submit', (event) => {
    let isValid = true; // Tracks overall form validity

    // Clear previous error messages
    clearErrors();

    // Validate email field
    if (!emailField.value.trim()) {
        showError(emailField, 'Email is required.');
        isValid = false;
    } else if (!validateEmail(emailField.value.trim())) {
        showError(emailField, 'Please enter a valid email address.');
        isValid = false;
    }

    // Validate password field
    if (!passwordField.value.trim()) {
        showError(passwordField, 'Password is required.');
        isValid = false;
    }

    // If the form is invalid, prevent submission
    if (!isValid) {
        event.preventDefault();
    }
});

// Function to display error messages
function showError(field, message) {
    const error = document.createElement('span');
    error.className = 'error-message';
    error.textContent = message;
    field.parentNode.insertBefore(error, field.nextSibling);
    field.classList.add('error-input');
}

// Function to clear all error messages
function clearErrors() {
    const errors = document.querySelectorAll('.error-message');
    errors.forEach(error => error.remove());

    const errorInputs = document.querySelectorAll('.error-input');
    errorInputs.forEach(input => input.classList.remove('error-input'));
}

// Helper function to validate email format
function validateEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}
