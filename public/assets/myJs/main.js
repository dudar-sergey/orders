window.addEventListener('load', () => {
    href();
    $('.tsort').tsort();
    $(".chosen-select").chosen({max_selected_options: 5});

})

function href(){
    let link = document.getElementsByClassName('nav-link')
    let pathname = window.location.pathname.split('/')
    for(let i = 0; i < link.length; i++){
        let currentLink = link[i].getAttribute('href').split('/')
        if (currentLink[1] === pathname[1]){
            link[i].classList.add('active');
        }
    }
}

function showMessage(message, time) {
    let notification = document.getElementById('notification')
    notification.classList.add('my-active')
    let divMessage = document.getElementById('message')
    divMessage.innerText = message
    setTimeout(() => {
        notification.classList.remove('my-active')
        message.innerText = ''
    }, time)
}