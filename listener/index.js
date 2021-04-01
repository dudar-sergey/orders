const axios = require('axios')
process.env.NODE_TLS_REJECT_UNAUTHORIZED='0'

const TEN_MINUTES = 60 * 10 * 1000
const ELEVEN_HOURS = 1000 * 60 * 60 * 11
const API_TOKEN = 'SECRET_WORD_FOR_MY_API322'
const URLS = {
    UPDATE_ALLEGRO_TOKENS: 'https://api288gg987124.greenauto.site/allegro_auth/update_tokens',
    UPDATE_ORDERS: 'https://api288gg987124.greenauto.site/order_api/update_order'
}

function updateToken() {
    return axios.get(URLS.UPDATE_ALLEGRO_TOKENS, {
        params: { API_TOKEN }
    })
}

function updateOrders() {
    return axios.get(URLS.UPDATE_ORDERS, {
        params: { API_TOKEN }
    })
}

async function startUpdateTokens() {
    await updateToken().catch(r => {
        console.log(r)
    })
    console.log('Token updated!')
    setTimeout(startUpdateTokens, ELEVEN_HOURS)
}

async function startUpdateOrders() {
    await updateOrders()
        .then(r => {
            console.log(r.data.message)
        })
        .catch(r => {
        console.log(r)
    })
    setTimeout(startUpdateOrders, TEN_MINUTES)
}
startUpdateTokens()
setTimeout(startUpdateOrders, 5000)


