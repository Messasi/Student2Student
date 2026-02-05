// Mobile sidbar and search toggle logic

document.addEventListener('DOMContentLoaded', () => {
    // Selectors
    const menu = document.getElementById('mobile-menu');
    const openBtn = document.getElementById('open-menu-btn');
    const closeBtn = document.getElementById('close-menu-btn');
    const searchToggle = document.getElementById('mobile-search-toggle');
    const searchBar = document.getElementById('mobile-search-bar');
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

  
    console.log('userMenuButton:', userMenuButton);
    console.log('userDropdown:', userDropdown);

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

    // User Dropdown Menu Logic 
    if (userMenuButton && userDropdown) {
        console.log('Initializing user dropdown...');
        
        userMenuButton.addEventListener('click', function(e) {
            console.log('User button clicked');
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            console.log('Dropdown hidden status:', userDropdown.classList.contains('hidden'));
        });
    } else {
        console.log('User dropdown elements not found - user may not be logged in');
    }

    // Initialise Lucide icons if the library is loaded
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    console.log('Student2Student marketplace loaded successfully');
});

// Close dropdown when clicking outside 
document.addEventListener('click', function(e) {
    const userDropdown = document.getElementById('user-dropdown');
    const userMenuButton = document.getElementById('user-menu-button');
    
    if (userDropdown && userMenuButton) {
        if (!userDropdown.contains(e.target) && !userMenuButton.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    }
});


//Validate form helper function

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


// Helper functions for form validation and formatting
function showFieldError(field, message) {
    clearFieldError(field);
    field.classList.add('border-red-500', 'border-2');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1 font-semibold field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

// Helper function to clear field errors
function clearFieldError(field) {
    field.classList.remove('border-red-500', 'border-2');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
}

// Helper function to format price values
function formatPrice(amount) {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
}

//helper function for smooth scrolling in settings 
// Helper function for smooth scrolling in settings 
document.querySelectorAll('.nav-link').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');

        // Check if the link is an internal anchor (starts with #)
        if (href.startsWith('#')) {
            e.preventDefault();
            
            const targetElement = document.querySelector(href);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
            
            // Update active state
            document.querySelectorAll('.nav-link').forEach(a => {
                a.classList.remove('bg-blue-50', 'text-[#0052FF]');
                a.classList.add('text-[#64748B]');
            });
            this.classList.add('bg-blue-50', 'text-[#0052FF]');
            this.classList.remove('text-[#64748B]');
        } 
      
    });
});