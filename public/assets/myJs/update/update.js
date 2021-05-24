window.addEventListener('load', () => {
    const updateQuantityBtn = document.getElementById('update-quantity-file-btn')
    updateQuantityBtn.addEventListener('click', updateQuantity)
    const imageBtn = document.getElementById('image-create-btn')
    imageBtn.addEventListener('click', createImage)
})

function createImage() {
    const profile = document.getElementById('image-profile').value
    const url = document.getElementById('image-url').value
    fetch('/product_api/images/create', {
        method: 'POST',
        body: JSON.stringify({
            profile,
            url
        })
    })
        .then((r) => {
            showModal('success', 'Изображения добавлены')
        })
}

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