document.addEventListener('click', function(e) {
    // On cible les liens avec data-confirm
    const trigger = e.target.closest('[data-confirm]');
    
    if (trigger) {
        e.preventDefault(); // On bloque la redirection index.php immédiate

        const message = trigger.getAttribute('data-confirm');
        const url = trigger.getAttribute('href');

        // 1. On met à jour le texte et le lien du bouton "Confirmer" dans la modale
        document.getElementById('confirmModalMessage').textContent = message;
        document.getElementById('confirmModalBtn').setAttribute('href', url);

        // 2. On affiche la modale avec l'API Bootstrap
        const myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        myModal.show();
    }
}, true);