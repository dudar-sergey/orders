window.addEventListener('load', () => {
    const cards = [...document.getElementsByClassName('sale-card')]
    cards.forEach(card => {
        card.addEventListener('click', () => {
            getSaleData(card.dataset.id)
                .then(async r => {
                    Swal.fire({
                        title: 'Карта продажи',
                        html: await r.text(),
                        width: '80%',
                        height: '80%',
                        animation: false,
                        showCloseButton: true,
                    })
                })
        })
    })
})

function getHtmlForModal(saleId) {
    getSaleData(saleId)
        .then(async r => {
            return await r.text()
        })
}

async function getSaleData(saleId) {
    return fetch('/api/get_sale_html/'+saleId, {
        method: 'GET'
    })
}
