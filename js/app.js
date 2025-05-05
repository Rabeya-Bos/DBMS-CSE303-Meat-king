$(document).ready(function() {
    // Add to cart
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('id');
        const quantity = $('#product-quantity').length ? parseInt($('#product-quantity').val()) : 1;
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: productId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#cart-count').text(response.count);
                    
                    // Show toast message
                    showToast('Product added to cart successfully!');
                }
            }
        });
    });
    
    // Function to show toast message
    function showToast(message) {
        const toast = `
            <div class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        $('.toast-container').html(toast);
        const toastElement = $('.toast');
        const bsToast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        bsToast.show();
    }
    
    // Star rating functionality
    $('.rating-selector .stars input').on('change', function() {
        const value = $('input[name="rating"]:checked').val();
        $('#rating-text').text(`${value} star${value > 1 ? 's' : ''}`);
    });
});