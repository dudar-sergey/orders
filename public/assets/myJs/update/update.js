window.addEventListener('load', () => {
    const updateQuantityBtn = document.getElementById('update-quantity-file-btn')
    updateQuantityBtn.addEventListener('click', updateQuantity)
})

function updateQuantity() {
    const updateQuantityForm = document.getElementById('update-quantity-form')
    let formData = new FormData(updateQuantityForm)
    sendUpdate(formData)
        .then(r => r.json())
        .then(data => {
            if(data.length === 0) {
                showModal('error', 'Товаров в файле нет')
            } else {
                showModal('success', 'Товаров обновлено: ' + data.length)
            }
        })
}

function sendUpdate(formData) {
    return fetch('/product_api/update_quantity', {
        method: 'POST',
        body: formData
    })
}

function showModal(success, text) {
    Swal.fire({
        text: text,
        icon: success
    })
}