window.addEventListener('load', () => {
    let updateButton = document.getElementById('update-btn')
    updateButton.addEventListener('click', () => {
        updateProduct(updateButton.value)
    })
})

async function sendRequest(data, productId) {
    const name = data.name
    const quantity = data.quantity
    return fetch('/api/update_product/'+productId+'?new_name='+name+'&quantity='+quantity, {
        method: 'GET'
    })
}

function updateProduct(productId) {
    const name = document.getElementById('name').value
    const quantity = document.getElementById('quantity').value
    const data = {
        quantity,
        name,
    }
    sendRequest(data, productId)
        .then(async r => {
            const response = await r.json()
            showMessage(response.message)
        })
}

function showMessage(message) {
    let html = '<div class="alert alert-success" role="alert">\n' +
        message +
        '</div>'
    let alert = $('#alert');
    alert.fadeOut('fast', function() {
        alert.html(html);
        alert.fadeIn('fast');
    })
    setInterval(() => {
        $('.alert').fadeOut()
    }, 4000)
}