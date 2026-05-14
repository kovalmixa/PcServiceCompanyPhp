function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');

    const input = document.querySelector('input[name="csrf_token"]');
    if (input) return input.value;

    console.error('CSRF token not found in page.');
    return null;
}

export async function sendActionRequest(url, method, data = null) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const options = {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': token
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const result = await response.json();
        if (response.ok && result.success) {
            return result;
        } else {
            const errorMsg = result.error || `Server error: ${response.status}`;
            console.error(errorMsg);
            alert(errorMsg);
            return null;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        return null;
    }
}

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
