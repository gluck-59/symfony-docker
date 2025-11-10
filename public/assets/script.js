const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
document.documentElement.setAttribute('data-bs-theme', userPrefersDark ? 'dark' : 'light');

document.addEventListener('DOMContentLoaded', function () {
    let toastElList = [].slice.call(document.querySelectorAll('.toast'));
    let toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 10000
        });
    });

    setTimeout(function (){
        toastList.forEach(toast => toast.show());
    }, 1000);


    initRequestFormDynamicEquipment();
});

function initRequestFormDynamicEquipment() {
    const customerSelect = document.querySelector('[data-request-customer]');
    const equipmentSelect = document.querySelector('[data-request-equipment]');

    if (!customerSelect || !equipmentSelect) {
        return;
    }

    const placeholderText = equipmentSelect.dataset.equipmentPlaceholder || '';
    const loadingText = equipmentSelect.dataset.equipmentLoadingText || 'Загрузка...';
    const emptyText = equipmentSelect.dataset.equipmentEmptyText || 'Оборудование не найдено';
    const errorText = equipmentSelect.dataset.equipmentErrorText || 'Ошибка загрузки оборудования';
    const urlTemplate = equipmentSelect.dataset.equipmentUrlTemplate || '';

    function refreshPicker() {
        // noop: refresh not needed without selectpicker
    }

    function setOptions(options, disableSelect, noneSelectedText) {
        equipmentSelect.innerHTML = '';

        if (noneSelectedText) {
            equipmentSelect.setAttribute('data-none-selected-text', noneSelectedText);
        }

        options.forEach(function (option) {
            const optionEl = document.createElement('option');
            optionEl.value = option.value;
            optionEl.textContent = option.label;
            if (option.disabled) {
                optionEl.disabled = true;
            }
            if (option.selected) {
                optionEl.selected = true;
            }
            equipmentSelect.appendChild(optionEl);
        });

        equipmentSelect.disabled = disableSelect;
        // noop: init not needed without selectpicker
    }

    function showPlaceholder(text, disableSelect = true) {
        setOptions([
            { value: '', label: text, disabled: true, selected: true }
        ], disableSelect, text);
    }

    function populateEquipment(items) {
        const options = [{ value: '', label: placeholderText, disabled: true, selected: true }];
        items.forEach(function (item) {
            options.push({ value: String(item.id), label: item.name });
        });
        setOptions(options, false, placeholderText);
    }

    function handleError(text) {
        showPlaceholder(text);
    }

    function loadEquipment(customerId) {
        if (!urlTemplate) {
            handleError(errorText);
            return;
        }

        const requestUrl = urlTemplate.replace('__ID__', customerId);
        showPlaceholder(loadingText, true);

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Bad response');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data || !Array.isArray(data.items)) {
                    throw new Error('Invalid payload');
                }

                if (data.items.length === 0) {
                    showPlaceholder(emptyText);
                    return;
                }

                populateEquipment(data.items);
            })
            .catch(function () {
                handleError(errorText);
            });
    }

    customerSelect.addEventListener('change', function () {
        const customerId = this.value;

        if (!customerId) {
            showPlaceholder(placeholderText);
            return;
        }

        loadEquipment(customerId);
    });

    if (!customerSelect.value) {
        showPlaceholder(placeholderText);
    } else if (equipmentSelect.dataset.disableAutoLoad === '1') {
        loadEquipment(customerSelect.value);
    }
}
