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

document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.like-button');
    const storedLikes = JSON.parse(localStorage.getItem('likes')) || {};

    likeButtons.forEach(button => {
        const cardId = button.getAttribute('data-id');
        const likeCountElement = document.querySelector(`.like-count[data-id="${cardId}"]`);
        let likeCount = storedLikes[cardId] || 0;

        likeCountElement.textContent = likeCount;

        button.addEventListener('click', function () {
            likeCount++;
            likeCountElement.textContent = likeCount;

            storedLikes[cardId] = likeCount;
            localStorage.setItem('likes', JSON.stringify(storedLikes));

            fetch('/update_likes.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cardId: cardId, likeCount: likeCount })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Like count updated successfully');
                    } else {
                        console.error('Failed to update like count');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});