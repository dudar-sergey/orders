window.addEventListener('load', () => {
    const searchInput = document.getElementById('search-input')
    const addButton = document.getElementById('add-button')
    const stateInput = document.getElementById('state')
    stateInput.addEventListener('input', () => {
        if(stateInput.value === '2' || stateInput.value === '3') {
            if(!document.getElementById('new-price')) {
                stateInput.insertAdjacentElement('afterend', createNewPriceInput())
            }
        } else {
            document.getElementById('new-price').remove()
        }
    })
    searchInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            showOrders(searchInput.value)
        }
    })
    searchInput.addEventListener('keyup', () => {
        if(searchInput.value === '') {
            clearTable()
        }
    })

    addButton.addEventListener('click', () => {
        addRefund()
    })
})

async function searchRequest(word, limit = null, offset = null) {
    let url = '/api/get_orders?type=html&word='+word
    if(limit) {
        url += '&limit=' + limit
    }
    if(offset) {
        url += '&offset=' + offset
    }
    return fetch(url, {
        method: 'POST',
    })
}

function showOrders(word) {
    let tableBody = document.getElementById('table-body')

    searchRequest(word)
        .then(async response => {
            tableBody.innerHTML = await response.text()
        })
}

function clearTable() {
    let tableBody = document.getElementById('table-body')
    tableBody.innerText = ''
}

function addRefund() {
    let ordersIds = []
    let reason = document.getElementById('reason').value
    let state = document.getElementById('state').value
    const checkboxes = [...document.getElementsByClassName('my-checkbox')]
    checkboxes.forEach(checkbox => {
        if(checkbox.checked) {
            ordersIds.push(checkbox.value)
        }
    })

    let requestBody = {
        orders: ordersIds,
        reason,
        state
    }

    fetch('/api/create_refund', {
        method: 'POST',
        body: JSON.stringify(requestBody)
    })
        .then(async response => {
            const data = await response.json()
            showMessage(data.message, 4000)
            addRefundToPage(data.refunds)
        })
}

function addRefundToPage(refunds) {
    let refundsBody = document.getElementById('refunds-body')
    let trs = []
    refunds.forEach(refund => {
        trs.push(createTrsForRefundsTable(refund.buyer, refund.productName, refund.date))
    })
    trs.forEach(tr => {
        refundsBody.appendChild(tr)
    })
}

function createTrsForRefundsTable(buyer, productName, date) {
    console.log(buyer, productName, date)
    let td1 = document.createElement('td')
    let td2 = document.createElement('td')
    let td3 = document.createElement('td')
    let tr = document.createElement('tr')
    td1.textContent = productName
    td2.textContent = buyer
    td3.textContent = date
    tr.appendChild(td1)
    tr.appendChild(td2)
    tr.appendChild(td3)
    return tr
}

function createNewPriceInput() {
    let input = document.createElement('input')
    input.type = 'text'
    input.placeholder = 'Введите новую цену для товара'
    input.classList.add('form-control', 'mt-2')
    input.id = 'new-price'

    return input
}


const allCheckbox = document.getElementById('all-checkbox')
allCheckbox.addEventListener('click', function () {
    if (allCheckbox.checked) {
        $('input:checkbox').each(function () {
            this.checked = true
        })
    } else {
        $('input:checkbox').each(function () {
            this.checked = false
        })
    }
})