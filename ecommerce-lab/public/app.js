// public/app.js

document.addEventListener('DOMContentLoaded', () => {

    // --- Task 2: Product Availability Toggle ---
    const toggleButtons = document.querySelectorAll('.toggle-availability');
    
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const button = this;

            // Simple AJAX request using fetch
            fetch(`?route=api/products/availability&id=${productId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({}) // empty body as we toggle based on current db state
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    if (data.is_available) {
                        button.textContent = 'In Stock';
                        button.classList.remove('out-of-stock');
                        button.classList.add('in-stock');
                    } else {
                        button.textContent = 'Out of Stock';
                        button.classList.remove('in-stock');
                        button.classList.add('out-of-stock');
                    }
                } else {
                    alert('Error toggling availability: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred.');
            });
        });
    });

});
