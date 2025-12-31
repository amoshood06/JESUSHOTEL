document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.add-to-cart-form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalButtonText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = 'Adding...';

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cart_item_count);
                    if (typeof showNotification === 'function') {
                        showNotification(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification(data.message, 'error');
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showNotification === 'function') {
                    showNotification('An error occurred while adding the item.', 'error');
                } else {
                    alert('An error occurred while adding the item.');
                }
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalButtonText;
            });
        });
    });

    function updateCartCount(count) {
        let cartCountSpan = document.querySelector('a[href="cart.php"] .bg-red-500');
        
        if (!cartCountSpan) {
            const cartButton = document.querySelector('a[href="cart.php"] button');
            if(cartButton) {
                 const newSpan = document.createElement('span');
                 newSpan.className = 'ml-1 px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full';
                 cartButton.appendChild(newSpan);
                 cartCountSpan = newSpan;
            }
        }

        if (cartCountSpan) {
            cartCountSpan.textContent = count;
            if (count > 0) {
                cartCountSpan.classList.remove('hidden');
            } else {
                cartCountSpan.classList.add('hidden');
            }
        }
    }
});
