function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');

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
