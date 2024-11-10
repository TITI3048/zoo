document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-btn');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('liked');
            if (button.classList.contains('liked')) {
                button.innerText = 'Liked!';
                button.classList.replace('btn-outline-primary', 'btn-primary');
            } else {
                button.innerText = 'Like';
                button.classList.replace('btn-primary', 'btn-outline-primary');
            }
        });
    });
});
const like = document.querySelector('.like');

let countLike = 0;
like.addEventListener('click', () => {

    if(countLike === 0) {
        like.classList.toggle('anim-like');
        countLike = 1;
        like.style.backgroundPosition = 'right';
    } else {
        countLike = 0;
        like.style.backgroundPosition = 'left';
    }

});

like.addEventListener('animationend', () => {
    like.classList.toggle('anim-like');
})


// Notifs 

const notif = document.querySelector('.notifications');
let countNotif = 0;

notif.addEventListener('click', () => {
    notif.classList.toggle('anim-notif')
    countNotif++;
    if(countNotif > 0){
        notif.style.backgroundPosition = 'right';
    }
})

notif.addEventListener('animationend', () => {
    notif.classList.toggle('anim-notif')
})

