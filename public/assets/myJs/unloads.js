window.addEventListener('load', () => {
    const unload = document.getElementById('unload')
    const nActive = document.getElementById('n-active')
    showUnloadProducts().then(() => {})

    unload.addEventListener('click', () => {
        nActive.classList.remove('active')
        unload.classList.add('active')
        showUnloadProducts().then(()  => {})
    })
    nActive.addEventListener('click', () => {
        unload.classList.remove('active')
        nActive.classList.add('active')
        showNActiveProducts()
    })


    const allCheckbox = document.getElementById('all')
    allCheckbox.addEventListener('click', function () {
        if(allCheckbox.checked) {
            $('input:checkbox').each(function() {
                this.checked = true
            })
        } else {
            $('input:checkbox').each(function() {
                this.checked = false
            })
        }
    })
})

function getUnloadProducts() {
    let html

    return ''
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

function showNActiveProducts() {
    let table = document.getElementById('unload-table');
    clearTable(table)
}

function clearTable(table) {
    table.innerText = ''
}
