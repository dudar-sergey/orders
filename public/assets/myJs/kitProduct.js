window.addEventListener('load', () => {
    let input = document.getElementById('search-select')
    let table = document.getElementById('table-body')
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            fetch('/api/products_for_select?word=' + input.value, {
                method: 'GET'
            })
                .then(async function (response) {
                    const result = await response.json()
                    addToSelect(result, table)
                })
        }
    })
    let button = document.getElementById('add-button')
    button.addEventListener('click', function () {
        sendKitRequest(button);
    })

    input.addEventListener('keyup', function () {
        if(input.value === '')
        clearTable(table)
    })
    $('.toast').toast({
        delay: 2000
    })
})

function addToSelect(result, table) {
    clearTable(table)
    for(let k = 0; k < result.length; k++) {
        let tr = document.createElement('tr')
        let ftd = document.createElement('td')
        let std = document.createElement('td')
        let checkbox = document.createElement('input')
        checkbox.type = 'checkbox'
        checkbox.value = result[k].id
        checkbox.classList.add('checkbox')
        ftd.appendChild(checkbox)
        std.innerText = result[k].name
        tr.appendChild(ftd);
        tr.appendChild(std)
        table.appendChild(tr);
    }
}

function clearTable(table) {
    table.innerText = ''
}

function sendKitRequest(button) {
    let checkboxes = [...document.getElementsByClassName('checkbox')]
    let articleInput = document.getElementById('article')
    let priceInput = document.getElementById('price')
    let quantityInput = document.getElementById('quantity')
    let requestBody = {
        products: [],
        options: {
            article: null,
            price: null,
            quantity: null,
        }
    }
    checkboxes.forEach(function (checkbox) {
        if(checkbox.checked) {
            requestBody.products.push(
                checkbox.value
            )
        }
    })
    requestBody.options.article = articleInput.value
    requestBody.options.price = priceInput.value
    requestBody.options.quantity = quantityInput.value
    console.log(requestBody)
    fetch('/api/create_kit', {
        method: 'POST',
        body: JSON.stringify(requestBody)
    }).then(async r => {
        r = await r.json()
        showNot(r.message)
    })
}

function showNot(message) {
    $('.toast-body').text(message)
    $('.toast').toast('show')
}



