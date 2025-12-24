// AVILLA OKADA HOTEL - JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    if (mobileMenuClose && mobileMenu) {
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });
    }

    // Close mobile menu when clicking outside
    if (mobileMenu) {
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Initialize tooltips
    initTooltips();

    // Initialize date pickers
    initDatePickers();

    // Initialize modals
    initModals();

    // Initialize search functionality
    initSearch();

    // Initialize cart functionality
    initCart();

    // Initialize booking system
    initBooking();

    // Initialize admin dashboard
    initAdminDashboard();

    // FAQ Toggle
    const faqTriggers = document.querySelectorAll('.faq-trigger');
    faqTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const svg = this.querySelector('svg');

            const isHidden = content.classList.contains('hidden');

            // Hide all other open FAQs
            document.querySelectorAll('.faq-content').forEach(c => {
                c.classList.add('hidden');
                const prevSvg = c.previousElementSibling.querySelector('svg');
                if (prevSvg) {
                    prevSvg.classList.remove('rotate-180');
                }
            });

            if (isHidden) {
                // Show this FAQ
                content.classList.remove('hidden');
                if (svg) {
                    svg.classList.add('rotate-180');
                }
            }
            // If it was already open, the above loop has already closed it.
        });
    });
});

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showError(field, 'This field is required');
            isValid = false;
        } else {
            clearError(field);
        }
    });

    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });

    // Phone validation
    const phoneFields = form.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(field => {
        if (field.value && !isValidPhone(field.value)) {
            showError(field, 'Please enter a valid phone number');
            isValid = false;
        }
    });

    return isValid;
}

function showError(field, message) {
    clearError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-600 text-sm mt-1 error-message';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.classList.add('border-red-500');
}

function clearError(field) {
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    field.classList.remove('border-red-500');
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute bg-gray-900 text-white px-2 py-1 rounded text-sm z-50 tooltip';
    tooltip.textContent = e.target.dataset.tooltip;
    document.body.appendChild(tooltip);

    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        input.min = today;

        // Add date picker styling
        input.classList.add('date-picker');
    });
}

function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        });
    });

    // Close modals
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop') || e.target.classList.contains('modal-close')) {
            const modal = e.target.closest('.modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    });
}

function initSearch() {
    const searchInput = document.querySelector('input[placeholder*="Search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
    }
}

function performSearch(query) {
    // Implement search functionality
    console.log('Searching for:', query);
    // This would typically make an AJAX request to search endpoint
}

function initCart() {
    // Cart functionality
    updateCartCount();

    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            const itemName = this.dataset.itemName;
            const itemPrice = this.dataset.itemPrice;

            addToCart(itemId, itemName, itemPrice);
        });
    });
}

function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('Item added to cart!', 'success');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.querySelector('.cart-count');

    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'inline' : 'none';
    }
}

function initBooking() {
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBooking(this);
        });
    }
}

function submitBooking(form) {
    const formData = new FormData(form);

    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        showNotification('Booking submitted successfully!', 'success');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        form.reset();
    }, 2000);
}

function initAdminDashboard() {
    // Admin dashboard specific functionality
    const tabs = document.querySelectorAll('.admin-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            switchTab(this);
        });
    });

    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initCharts();
    }
}

function switchTab(clickedTab) {
    // Remove active class from all tabs
    document.querySelectorAll('.admin-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-teal-50', 'text-teal-600');
        tab.classList.add('text-gray-600');
    });

    // Add active class to clicked tab
    clickedTab.classList.add('active', 'bg-teal-50', 'text-teal-600');
    clickedTab.classList.remove('text-gray-600');

    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Show selected tab content
    const targetId = clickedTab.getAttribute('href').substring(1);
    const targetContent = document.getElementById(targetId);
    if (targetContent) {
        targetContent.classList.remove('hidden');
    }
}

function initCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue (₦)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#0891b2',
                    backgroundColor: 'rgba(8, 145, 178, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Bookings Chart
    const bookingsCtx = document.getElementById('bookingsChart');
    if (bookingsCtx) {
        new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Bookings',
                    data: [12, 19, 15, 25, 22, 30, 18],
                    backgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 notification ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Utility functions
function debounce(func, wait) {
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// AJAX helper function
function ajax(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: null
    };

    const config = { ...defaultOptions, ...options };

    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
}

// Export functions for global use
window.AvillaHotel = {
    validateForm,
    showNotification,
    addToCart,
    ajax
};