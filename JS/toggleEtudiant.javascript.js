function toggleEtudiantFields() {
        const role = document.getElementById('role').value;

        const blocCVEC = document.getElementById('blocCVEC');
        const inputCVEC = document.getElementById('cvec');

        // La condition fonctionne maintenant car l'option HTML envoie "etudiant"
        if (role === 'etudiant') {
            blocCVEC.style.display = 'block';
            inputCVEC.required = true;
        } else {
            blocCVEC.style.display = 'none';
            inputCVEC.required = false;
            
            // Vide les champs si on repasse sur "Particulier" pour ne pas envoyer de fausses données
            inputCVEC.value = '';
        }
    }
    
    // Appel au chargement de la page pour restaurer l'état après une erreur de soumission
    toggleEtudiantFields();