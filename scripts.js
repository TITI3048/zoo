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
