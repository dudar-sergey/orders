window.addEventListener('load', () => {
    href();
    $('.tsort').tsort();
    $(".chosen-select").chosen({max_selected_options: 5});
})

function href(){
    let link = document.getElementsByClassName('nav-link')
    for(let i = 0; i < link.length; i++){
        if (link[i].getAttribute('href') === window.location.pathname){
            link[i].classList.add('active');
        }
    }
}