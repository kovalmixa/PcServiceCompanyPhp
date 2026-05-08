import { getValue, setValue } from './elements.js';

export function getTotal(pricePerUnit, quantity) {
    return pricePerUnit * quantity;
}

export function updatePriceLabel(labelId, value) {
    const numericValue = parseFloat(value);
    if (isNaN(numericValue)) {
        console.error(`Ошибка: передано нечисловое значение в updatePriceLabel для ${labelId}:`, value);
        return;
    }

    setValue(labelId, '$' + numericValue.toFixed(2));
}

window.getTotal = getTotal;
window.updatePriceLabel = updatePriceLabel;