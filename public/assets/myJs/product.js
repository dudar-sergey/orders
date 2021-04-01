const searchInput = document.getElementById('search');
let sort = {
    column: '',
    method: '',
}
window.addEventListener('load', () => {
    showProductsTable()
    const countSelects = [...document.getElementsByClassName('count-products-select')]
    countSelects.forEach(select => select.addEventListener('input', handleCountSelect))
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            showProductsTable(searchInput.value)
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

function paintTable(html) {
    let table = document.getElementById('mytable')

    table.innerHTML = html;

    let myPageLinks = [...document.getElementsByClassName('page-link')]
    myPageLinks.forEach(link => {
        link.addEventListener('click', function () {
            const page = link.value
            showProductsTable(searchInput.value, page)
        })
    })
    const ths = [...document.getElementsByTagName('th')]
    ths.forEach(th => {
        th.addEventListener('click', () => {
            sort.column = th.id
            if(sort.method === 'ASC' || sort.method === '') {
                sort.method = 'DESC'
            } else {
                sort.method = 'ASC'
            }
            showProductsTable(searchInput.value)
        })
    })
}

function showProductsTable(word = null, page = 1) {
    let url = '/api/get_products_html?p=' + page
    if (sort.column && sort.method) {
        url += '&sort=' + sort.column + '&method=' + sort.method
    }
    if (word) {
        url += '&word=' + word
    }
    fetch(url, {
        method: 'GET'
    })
        .then(async function (response) {
            paintTable(await response.text())
        })
}
