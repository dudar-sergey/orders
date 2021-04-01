window.addEventListener('load', () => {
    const addGroupBtn = document.getElementById('add-group-btn')
    addGroupBtn.addEventListener('click', createGroup)
})

function createGroup() {
    const nameGroup = document.getElementById('group-name-input')
    console.log('dsa')
    if(nameGroup.value) {
        sendGroupCreate(nameGroup.value)
            .then(r => r.json())
            .then(data => {
                printNewGroup(data.group)
                nameGroup.value = ''
            })
    }
}

function sendGroupCreate(ruName) {
    return fetch('/api/create_group', {
        method: 'POST',
        body: JSON.stringify({
            ruName
        })
    })
}

function printNewGroup(group) {
    let body = document.getElementById('description-body')
    body.innerHTML += getHTML(group.ruName, group.id)

}

function getHTML(name, id) {
    return '<tr>\n' +
        '                                        <td><b>'+id+'</b></td>\n' +
        '                                        <td>'+name+'</td>\n' +
        '                                        <td><a href="/book/'+id+'"><span class="badge badge-primary badge-pill ml-5">Изменить</span></a>\n' +
        '                                        </td>\n' +
        '                                    </tr>'
}