const searchInput = document.getElementById('search');
window.addEventListener('load', () => {
        showProductsTable()
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

    let myPageLinks = [...document.getElementsByClassName('page-link')]
    myPageLinks.forEach(link => {
        link.addEventListener('click', function () {
            const page = link.value
            showProductsTable(searchInput.value, page)
            table.scrollIntoView()
        })
    })
}

function showProductsTable(word = null, page = 1) {
    let url = '/api/get_products_html?p='+page
    if(word) {
        url += '&word='+word
    }
        fetch(url, {
            method: 'GET'
        })
            .then(async function (response) {
                paintTable(await response.text())
            })
}
