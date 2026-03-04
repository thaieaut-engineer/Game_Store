// Add to cart functionality
$(document).ready(function () {
    // Use event delegation for dynamically added elements
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const gameId = $btn.data('game-id');
        const quantity = $btn.data('quantity') || 1;

        console.log('Adding to cart:', gameId, quantity);

        // Disable button to prevent double click
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + 'cart/add',
            method: 'POST',
            dataType: 'json',
            data: {
                game_id: gameId,
                quantity: quantity
            },
            success: function (response) {
                // If dataType is 'json', response is already parsed
                const data = response;

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
                        setTimeout(function () {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                let errorMessage = 'Có lỗi kết nối. Vui lòng thử lại.';
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.message) errorMessage = resp.message;
                } catch (e) { }

                showNotification(errorMessage, 'error');
                $btn.prop('disabled', false).html('<i class="bi bi-cart-plus"></i>');
            }
        });
    });

    // Update cart quantity using delegation
    $(document).on('change', '.update-quantity', function () {
        const itemId = $(this).data('item-id');
        const quantity = $(this).val();

        $.ajax({
            url: BASE_URL + 'cart/update',
            method: 'POST',
            dataType: 'json',
            data: {
                item_id: itemId,
                quantity: quantity
            },
            success: function (data) {
                if (data.success) {
                    location.reload(); // Reload to update all prices
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            },
            error: function (xhr) {
                console.error('AJAX Update Error:', xhr.responseText);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });

    // Remove cart item using delegation
    $(document).on('click', '.remove-item', function (e) {
        e.preventDefault();
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            return;
        }

        const itemId = $(this).data('item-id');
        console.log('Removing item:', itemId);

        $.ajax({
            url: BASE_URL + 'cart/remove',
            method: 'POST',
            dataType: 'json',
            data: {
                item_id: itemId
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            },
            error: function (xhr) {
                console.error('AJAX Remove Error:', xhr.responseText);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });

    // Auto-dismiss alerts
    setTimeout(function () {
        $('.alert:not(.position-fixed)').fadeOut();
    }, 5000);
});

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

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

    setTimeout(function () {
        notification.fadeOut(function () {
            $(this).remove();
        });
    }, 3000);
}

// Update cart count in header
function updateCartCount(count) {
    const $cartLink = $('#cart-link');
    let $badge = $cartLink.find('.badge');

    if (count > 0) {
        if ($badge.length === 0) {
            $cartLink.append(' <span class="badge bg-danger ms-1" id="cart-count">' + count + '</span>');
        } else {
            $badge.text(count);
        }
    } else {
        $badge.remove();
    }
}
