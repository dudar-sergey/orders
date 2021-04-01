window.addEventListener('load', () => {
    const unload = document.getElementById('unload')
    const nActive = document.getElementById('n-active')
    showUnloadProducts().then(() => {
    })
    const uploadButton = document.getElementById('upload-button')
    const activateButton = document.getElementById('activate-button')

    unload.addEventListener('click', () => {
        nActive.classList.remove('active')
        unload.classList.add('active')
        showUnloadProducts().then(() => {
        })
    })
    nActive.addEventListener('click', () => {
        unload.classList.remove('active')
        nActive.classList.add('active')
        showNActiveProducts().then(r => {})
    })

    uploadButton.addEventListener('click', () => {
        sendProductsToUpload()
    })
    activateButton.addEventListener('click', () => {
        sendProductsToActivate()
    })

})

function sendProductsToActivate() {
    let currentProducts = [...document.getElementsByClassName('upload-checkbox')]
    let requestBody = {
        products: []
    }
    let trs = []

    currentProducts.forEach(el => {
        if(el.checked) {
            requestBody.products.push(el.value)
            trs.push(el.parentElement.parentElement)
        }
    })

    fetch('/api/change_offer_status?command=ACTIVATE', {
        method: 'POST',
        body: JSON.stringify(requestBody.products)
    })
        .then(r => {
            trs.forEach(tr => {
                tr.remove()
            })
        })
}

function sendProductsToUpload() {
    let currentProducts = [...document.getElementsByClassName('upload-checkbox')]
    let requestBody = {
        products: []
    }

    currentProducts.forEach(function (el) {
        if (el.checked) {
            requestBody.products.push(el.value)
        }
    })

    fetch('/api/upload_to_allegro', {
        method: 'POST',
        body: JSON.stringify(requestBody)
    }).then(r => {
    })
}

async function showUnloadProducts() {
    let table = document.getElementById('unload-table');
    clearTable(table)
    fetch('/api/get_unload_products', {
        method: 'GET'
    })
        .then(async r => {
            table.innerHTML = await r.text();
        })
}

async function showNActiveProducts() {
    let table = document.getElementById('unload-table');
    clearTable(table)
    fetch('/api/get_nonactivate_products', {
        method: 'GET'
    })
        .then(async r => {
            table.innerHTML = await r.text();
        })

}

function clearTable(table) {
    table.innerText = ''
}


const allCheckbox = document.getElementById('all')
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
