window.addEventListener('load', () => {
        const searchInput = document.getElementById('search');
        const countSelects = [...document.getElementsByClassName('count-products-select')]
        countSelects.forEach(select => select.addEventListener('input', handleCountSelect))
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                showProductsTableSearch(searchInput.value)
            }
        })
})


function redirect(source) {
    window.location.href = source
}

function handleCountSelect({target}) {
    const value = target.value

    return fetch('/api/change_limit_for_products?limit=' + value, {
        method: 'GET',
    })
        .then(() => redirect('/products'))
}


function showProductsTableSearch(word, page = null) {
    requestSearch(word, page)
        .then(async function (response) {
            paintTable(await response.text())
        })
}


function requestSearch(word, page = null) {
    let url = '/api/get_products_html?word=' + word;
    if (page) {
        url += '&p=' + page;
    }
    return fetch(url, {
        method: 'GET'
    })
}

function paintTable(html) {
    let table = document.getElementById('mytable')

    table.innerHTML = html;
}

function showProductsTable(word = null, page = null) {
    if (word === null) {
        fetch('/api/get_products_html?p=1', {
            method: 'GET'
        })
            .then(async function (response) {
                paintTable(await response.text())
            })
    } else {
        showProductsTableSearch(word, page)
    }
}
