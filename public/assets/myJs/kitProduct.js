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
        if (input.value === '')
            clearTable(table)
    })
    $('.toast').toast({
        delay: 2000
    })

    let genArticleButton = document.getElementById('gen-article')
    genArticleButton.addEventListener('click', function () {
        let articleInput = document.getElementById('article')
        articleInput.value = generateArticle()
    })

    let imageInput = document.getElementById('add-image')
    imageInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            createImage(imageInput)
        }
    })
})

function createImage(input) {
    let imageGallery = document.getElementById('image-gallery')
    if (input !== '') {
        let image = document.createElement('img')
        image.src = input.value
        image.classList.add('m-3', 'image')
        image.style.height = '150px'
        image.style.width = '150px'
        image.style.borderRadius = '10px'
        imageGallery.appendChild(image)
        input.value = ''
        image.addEventListener('click', () => {
            image.animate([
                // keyframes
                {transform: 'scale(0.1)'}
            ], {
                // timing options
                duration: 200,
            })
            setTimeout(function () {
                image.remove()
            }, 200)
        })
    }
}

function addToSelect(result, table) {
    clearTable(table)
    for (let k = 0; k < result.length; k++) {
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
    let imagesSrc = []
    let images = [...document.getElementsByClassName('image')]
    images.forEach(image => {
        imagesSrc.push(image.getAttribute('src'))
    })
    console.log(imagesSrc)
    let checkboxes = [...document.getElementsByClassName('checkbox')]
    let articleInput = document.getElementById('article')
    let priceInput = document.getElementById('price')
    let descriptionInput = document.getElementById('description')
    let requestBody = {
        products: [],
        options: {
            article: null,
            price: null,
        }
    }
    checkboxes.forEach(function (checkbox) {
        if (checkbox.checked) {
            requestBody.products.push(
                checkbox.value
            )
        }
    })
    requestBody.options.images = imagesSrc
    requestBody.options.article = articleInput.value
    requestBody.options.price = priceInput.value
    requestBody.options.description = descriptionInput.value
    console.log(descriptionInput.value)
    if (articleInput.value !== '') {
        fetch('/api/create_kit', {
            method: 'POST',
            body: JSON.stringify(requestBody)
        }).then(async r => {
            r = await r.json()
            showNot(r.message)
        })
    } else {
        showNot('Введите артикул')
        articleInput.style.borderColor = 'red'
        setTimeout(function () {
            articleInput.style.borderColor = ''
        }, 1000)
    }
}

function showNot(message) {
    $('.toast-body').text(message)
    $('.toast').toast('show')
}

function generateArticle() {
    const prefix = 'DMK'
    const serial = '06'
    let date = new Date()
    let hours = ('0' + date.getHours()).substr(-2)
    let second = ('0' + date.getSeconds()).substr(-2)
    let day = ('0' + date.getDay()).substr(-1)
    return prefix + serial + hours + second + day
}



