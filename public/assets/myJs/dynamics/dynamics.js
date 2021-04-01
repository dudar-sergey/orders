let inProgress = false
window.addEventListener('load', () => {
    const dates = [...document.getElementsByClassName('dates')]
    dates.forEach(date => date.addEventListener('input', updateInformation))

    const testBtn = document.getElementById('test')
    testBtn.addEventListener('click', init)
})

function updateInformation() {
    const startDate = document.getElementById('start-date').value
    const endDate = document.getElementById('end-date').value
    getInformation(startDate, endDate)
        .then(r => r.text())
        .then(data => {
            printTable(data)
        })
}

function printTable(html) {
    let tableBody = document.getElementById('table-body')
    tableBody.innerHTML = html
}

function getInformation(startDate, endDate) {
    return fetch('/api/get_dynamics_html?startDate=' + startDate + '&endDate=' + endDate, {
        method: 'GET'
    })
}


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
    setTimeout(() => {doIt(processId)
        .then(r => {
            inProgress = false
        })}, 1000)
}

function getHtml() {
    return '<div class="container"><div class="progress">\n' +
        '  <div id="progress" class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>\n' +
        '</div></div></div>'
}

async function initProgress(processId) {
    return fetch('/api/progress/init_progress?process_id=' + processId)
}

function getProcessId() {
    return Math.floor(Math.random() * 1000)
}

function doIt(processId) {
    return fetch('/api/progress/test?processId=' + processId);
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
                })
        }
    }, 1000)
}