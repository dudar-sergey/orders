window.addEventListener('load', () => {
    const paymentSelects = [...document.getElementsByClassName('payment-select')]
    paymentSelects.forEach(select => select.addEventListener('input', handleSelect))
    const buttonsCard = [...document.getElementsByClassName('modalCard')]
    buttonsCard.forEach(button => button.addEventListener('click', function () {
        $('#myModal').modal('show')
        $('#modal-body').text(button.getAttribute('data-id'))
    }))
})

function handleSelect({target}) {
    const row = target.closest('tr')
    const paymentStatusId = target.value
    const orderId = row.dataset.id

    if (paymentStatusId === '-1') {
        sendRequest(null, orderId)
            .then(() => eraseRowColor(row))
            .catch(() => fillError(row))
    } else {
        sendRequest(paymentStatusId, orderId)
            .then(() => fillSuccess(row))
            .catch(() => fillError(row))
    }
}

function eraseRowColor(row) {
    row.classList.remove('table-success', 'table-danger', 'table-warning')
}

function fillWarning(row) {
    eraseRowColor(row)
    row.classList.add('table-warning')
}

function fillSuccess(row) {
    eraseRowColor(row)
    row.classList.add('table-success')
}

function fillError(row) {
    eraseRowColor(row)
    row.classList.add('table-danger')
}

function sendRequest(paymentStatusId, orderId) {
    return fetch('/api/change_order_status', {
        method: 'POST',
        body: JSON.stringify({
            paymentStatusId,
            orderId
        })
    }).then(handleError)
}

function handleError(response) {
    if (!response.ok) {
        throw new Error(response.statusText)
    }
}