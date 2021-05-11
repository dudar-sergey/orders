const syncBtn = document.getElementById('sync-quantity-btn')
let inProgress = false
window.addEventListener('load', () => {
    let listProfiles = [...document.getElementsByClassName('profile-item')]
    listProfiles.forEach(profileItem => {
        profileItem.addEventListener('click', () => {
            listProfiles.forEach(profile => {
                profile.classList.remove('selected')
            })
            setProfileInSession(profileItem)
        })
    })
    syncBtn.addEventListener('click', init)
})

async function init() {
    const processId = getProcessId()
    inProgress = true;
    await initProgress(processId)
        .then(r => {
            Swal.fire({
                icon: 'success',
                confirmButtonText: 'Ок',
                html: getHtml()
            })

        })
    check(processId)
    setTimeout(() => {syncQuantity(processId)
        .then(r => {
            inProgress = false
        })}, 1000)
}

function setProfileInSession(profileItem) {
    let clientIdInput = document.getElementById('client-id')
    let clientSecretInput = document.getElementById('client-secret')
    let authUrlButton = document.getElementById('auth-url')
    fetch('/api/set_profile?profile_id='+profileItem.value, {
        method: "GET"
    })
        .then(async response => {
            profileItem.classList.add('selected')
            const data = await response.json()
            clientIdInput.value = data.profile.clientId
            clientSecretInput.value = data.profile.clientSecret
            const url = 'https://allegro.pl/auth/oauth/authorize?response_type=code&client_id='+data.profile.clientId+'&redirect_uri=https://api288gg987124.greenauto.site/allAuth&promt=none'
            authUrlButton.setAttribute('href', url)
        })
}

function syncQuantity(processId) {
    const currentProfileId = document.querySelector('.profile-item.selected').value
    console.log(currentProfileId)
    return fetch('/api/sync_quantity_allegro/'+currentProfileId+'?process_id='+processId)
}


function getHtml() {
    return '<div class="container"><div class="progress">\n' +
        '  <div id="progress" class="progress-bar" role="progressbar" style="width: 0;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>\n' +
        '</div></div></div><div id="progress-body"></div>'
}

async function initProgress(processId) {
    return fetch('/api/progress/init_progress?process_id=' + processId)
}

function getProcessId() {
    return Math.floor(Math.random() * 1000)
}

async function checkProgress(processId) {
    return fetch('/api/progress/check_progress?process_id=' + processId)
}

function check(processId) {
    setInterval(() => {
        if (inProgress) {
            checkProgress(processId)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('progress').setAttribute('aria-valuenow', data.percent)
                    document.getElementById('progress').style.width = data.percent+'%'
                    document.getElementById('progress').innerText = data.percent+'%'
                    document.getElementById('progress-body').innerText = data.message
                })
        }
    }, 100)
}