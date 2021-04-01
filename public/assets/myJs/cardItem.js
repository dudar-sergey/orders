window.addEventListener('load', () => {
    let updateButton = document.getElementById('update-btn')
    updateButton.addEventListener('click', () => {
        updateProduct(updateButton.value)
    })

    const statusSelect = document.getElementById('status-select')
    if(statusSelect) {
        statusSelect.addEventListener('input', () => {
            updateStatus(statusSelect)
        })
    }
    const groupSelect = document.getElementById('group-select')
    groupSelect.addEventListener('input', () => {
        changeGroup(groupSelect.value, updateButton.value)
            .then(r => r.json())
            .then(data => {
                showMessage(data.message, 4000)
            })
    })
})

function changeGroup(id, productId) {
    return fetch('/product_api/change_group/'+id+'?product_id='+productId)
}

async function sendRequest(data, productId) {
    const name = data.name
    const quantity = data.quantity
    const allegroName = data.allegroName
    return fetch('/api/update_product/'+productId+'?new_name='+name+'&quantity='+quantity+'&allegro_name='+allegroName, {
        method: 'GET'
    })
}

function updateProduct(productId) {
    const name = document.getElementById('name').value
    const quantity = document.getElementById('quantity').value
    const allegroName = document.getElementById('allegro-name').value
    const data = {
        quantity,
        name,
        allegroName,
    }
    sendRequest(data, productId)
        .then(async r => {
            const response = await r.json()
            showMessage(response.message, 5000)
        })
}

function updateStatus(statusSelect) {
    const productId = statusSelect.dataset.id
    let command = ''
    if(statusSelect.value === '0') {
        command = 'END'
    } if (statusSelect.value === '1') {
        command = 'ACTIVATE'
    }
    sendRequestChangeStatus({
        id: productId
    }, command)
        .then(async r => {
            const response = await r.json()
            showMessage('Команда зарегистрирована', 5000)
        })
}

async function sendRequestChangeStatus(productIds, command) {
    return fetch('/api/change_offer_status?command='+command, {
        method: 'POST',
        body: JSON.stringify(productIds)
    })
}