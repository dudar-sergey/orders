window.addEventListener('load', () => {
    const btn = document.getElementById('send-btn')

    btn.addEventListener('click', () => {
        let formData = new FormData(document.getElementById('supply-form'))
        sendProducts(formData)
            .then(r => r.json())
            .then(data => {
                Swal.fire({
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Ок'
                })
            })
    })
})

function sendProducts(formData) {
    return fetch('/product_api/upload_products', {
        method: 'POST',
        body: formData
    })
}