window.addEventListener('load', () => {
    let searchInput = document.getElementById('product-search')
    let sendButton = document.getElementById('send-button')

    searchInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            printProductsInTable(searchInput.value)
        }
        if(searchInput.value === '') {
            clearTable()
        }
    })

    sendButton.addEventListener('click', () => {
        sendSale()
    })
})

function printProductsInTable(word) {
    let table = document.getElementById('table-body')

    fetch('/product_api/get_table_product_art_name?word='+word, {
        method: "GET"
    })
        .then(async r => {
            table.innerHTML = await r.text()
        })
}

function clearTable() {
    let table = document.getElementById('table-body')
    table.innerText = ''
}

function sendSale() {
    let product = null
    let checkboxes = [...document.getElementsByClassName('my-checkbox')]
    checkboxes.forEach(function (checkbox) {
        if(checkbox.checked) {
            product = checkbox.value
        }
    })
    let buyer = document.getElementById('buyer').value
    let platform = document.getElementById('sale-platform').value
    let price = document.getElementById('sale-price').value
    let quantity = document.getElementById('sale-quantity').value
    if(buyer && platform && quantity && price) {
        let requestBody = {
            product,
            buyer,
            platform,
            quantity,
            price
        }

        fetch('/api/create_sale', {
            method: 'POST',
            body: JSON.stringify(requestBody)
        })
            .then(async r => {
                let data = await r.json()
                showMessage(data.message, 5000)
            })
    }
}