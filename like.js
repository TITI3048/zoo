document.addEventListener('DOMContentLoaded', () => {
    // Sélectionner tous les boutons de like
    const likeButtons = document.querySelectorAll('.like-button');

    // Ajouter un gestionnaire d'événement pour chaque bouton
    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const likeCount = button.previousElementSibling;
            const currentCount = parseInt(likeCount.textContent);
            likeCount.textContent = currentCount + 1;

            // Animation sur l'icône de like
            const likeIcon = button.querySelector('.like-icon');
            likeIcon.classList.add('liked-animation');
            setTimeout(() => {
                likeIcon.classList.remove('liked-animation');
            }, 300);
        });
    });
});
