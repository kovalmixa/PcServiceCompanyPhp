/**
 * data-base.js
 * Core HTTP helpers – PHP backend edition.
 *
 * PHP CSRF setup (add to _layout.php <head>):
 *   <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
 *
 * Or use the hidden input already rendered by index.php:
 *   <input type="hidden" name="csrf_token" value="...">
 *
 * Both are checked below (meta tag takes priority).
 */

function getCsrfToken() {
    // 1. Prefer <meta name="csrf-token"> (cleaner, layout-level)
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');

    // 2. Fall back to hidden input rendered by the view
    const input = document.querySelector('input[name="csrf_token"]');
    if (input) return input.value;

    console.error('CSRF token not found in page.');
    return null;
}

export async function sendActionRequest(url, method, data = null) {
    const token = getCsrfToken();

    if (!token) return null;

    const options = {
        method: method,
        headers: {
            'X-CSRF-Token': token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        if (response.ok) {
            return response;
        } else {
            const errorText = await response.text();
            console.error(`Error ${response.status}: ${errorText}`);
            alert(`Error: ${response.status}`);
            return null;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        return null;
    }
}

/**
 * Place an order (customer cart or staff component order).
 *
 * PHP endpoints (placeholders – implement in your router):
 *   POST /client/orders/add-to-cart/{id}?quantity={q}
 *   POST /staff/component-order/order/{id}?quantity={q}
 */
export async function handleOrder(id, quantity, companyId, isComponent, role = 'Customer') {
    const isCustomer = (role === 'Customer');
    const url = isCustomer
        ? '/client/orders/add-to-cart'
        : '/staff/component-order/order';

    const response = await sendActionRequest(
        `${url}/${id}?quantity=${quantity}`,
        'POST',
        isCustomer ? { id, quantity, companyId, isComponent } : null
    );

    if (!response) return;
    const result = await response.json();
    if (result.success) {
        alert(isCustomer ? 'New item added to cart' : 'Order has been processed');
    }
}

window.handleOrder = handleOrder;
window.sendActionRequest = sendActionRequest;
