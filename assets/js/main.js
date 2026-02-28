// Add to cart functionality
$(document).ready(function() {
    // Use event delegation for dynamically added elements
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const gameId = $btn.data('game-id');
        const quantity = $btn.data('quantity') || 1;
        
        // Disable button to prevent double click
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: BASE_URL + 'cart/add',
            method: 'POST',
            data: {
                game_id: gameId,
                quantity: quantity
            },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    // Show success message
                    showNotification(data.message || 'Đã thêm vào giỏ hàng', 'success');
                    
                    // Update cart count in header
                    if (data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    
                    // Re-enable button
                    $btn.prop('disabled', false).html('<i class="bi bi-cart-plus"></i>');
                } else {
                    showNotification(data.message || 'Có lỗi xảy ra', 'error');
                    
                    // Re-enable button
                    $btn.prop('disabled', false).html('<i class="bi bi-cart-plus"></i>');
                    
                    // Redirect if needed
                    if (data.redirect) {
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                }
            },
            error: function(xhr, status, error) {
                showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                $btn.prop('disabled', false).html('<i class="bi bi-cart-plus"></i>');
            }
        });
    });
    
    // Update cart quantity
    $('.update-quantity').on('change', function() {
        const itemId = $(this).data('item-id');
        const quantity = $(this).val();
        
        $.ajax({
            url: BASE_URL + 'cart/update',
            method: 'POST',
            data: {
                item_id: itemId,
                quantity: quantity
            },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    $('#cart-total').text(formatCurrency(data.total));
                    location.reload(); // Reload to update all prices
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });
    
    // Remove cart item
    $('.remove-item').on('click', function() {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            return;
        }
        
        const itemId = $(this).data('item-id');
        
        $.ajax({
            url: BASE_URL + 'cart/remove',
            method: 'POST',
            data: {
                item_id: itemId
            },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });
    
    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

// Set BASE_URL for JavaScript
const BASE_URL = 'http://localhost/Game_Store/';

// Show notification
function showNotification(message, type) {
    type = type || 'info';
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>');
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}

// Update cart count in header
function updateCartCount(count) {
    const $cartLink = $('a[href*="cart"]');
    let $badge = $cartLink.find('.badge');
    
    if (count > 0) {
        if ($badge.length === 0) {
            $cartLink.append(' <span class="badge bg-danger">' + count + '</span>');
        } else {
            $badge.text(count);
        }
    } else {
        $badge.remove();
    }
}
