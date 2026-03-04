document.addEventListener("DOMContentLoaded", function() {
    const items = document.querySelectorAll('.annonce-item');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    // Nombre d'annonces à afficher par défaut
    let currentVisible = 6; 
    // Nombre d'annonces supplémentaires à charger au clic
    const step = 6; 

    function updateVisibility() {
        items.forEach((item, index) => {
            if (index < currentVisible) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });

        // Masquer le bouton s'il n'y a plus rien à charger
        if (currentVisible >= items.length) {
            loadMoreContainer.style.display = 'none';
        } else {
            loadMoreContainer.style.display = 'block';
        }
    }

    // Initialisation
    if (items.length > 0) {
        updateVisibility();
    }

    // Evénement au clic sur le bouton "Voir plus"
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentVisible += step;
            updateVisibility();
        });
    }
});