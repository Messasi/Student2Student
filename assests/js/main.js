/**
 * Student2Student - Main JavaScript File
 * Handles global interactions and UI logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // Selectors
    const menu = document.getElementById('mobile-menu');
    const openBtn = document.getElementById('open-menu-btn');
    const closeBtn = document.getElementById('close-menu-btn');
    const searchToggle = document.getElementById('mobile-search-toggle');
    const searchBar = document.getElementById('mobile-search-bar');

    // Mobile Sidebar Logic
    if (openBtn && menu) {
        openBtn.addEventListener('click', () => {
            menu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden'; 
            if (searchBar) searchBar.classList.add('hidden');
        });
    }

    if (closeBtn && menu) {
        closeBtn.addEventListener('click', () => {
            menu.classList.add('translate-x-full');
            document.body.style.overflow = ''; 
        });
    }

    // Mobile Search Toggle Logic
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', () => {
            const isHidden = searchBar.classList.toggle('hidden');
            if (!isHidden) {
                const input = searchBar.querySelector('input');
                if (input) input.focus();
            }
        });
    }

    console.log('Student2Student marketplace loaded successfully');
});

/**
 * Form Validation and Utility Functions
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    field.classList.add('border-red-500', 'border-2');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1 font-semibold field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('border-red-500', 'border-2');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
}

function formatPrice(amount) {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
}

function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}