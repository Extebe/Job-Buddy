<?php

class ControllerNote extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        $template = $this->getTwig();

        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        //recupération des annonces
        $managerNote = new NoteDao($this->getPdo());
        $tableau = $managerNote->findByUser('1');
        $notes = $managerNote->hydrateAll($tableau);
      
        echo $template->render('pageDeNote.html.twig', [
            'role' => $role,
            'notes' => $notes
            //'annonces' => $annonces
        ]);


    }

    public function saisieNote(){
        $template = $this->getTwig();

        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }
        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $annonce = $managerAnnonce->find(1);
        //recupération des annonces
        $managerNote = new NoteDao($this->getPdo());
        $managerParticulier = new ParticulierDAO($this->getPdo());
        $managerEtudiant = new EtudiantDAO($this->getPdo());

        if ($role == 'Etudiant'){
            $Auteur = $managerEtudiant->findByAnnonce($annonce->getId());
            $Receveur = $managerParticulier->findByAnnonce($annonce->getId());

        }
        else{
            $Auteur = $managerParticulier->findByAnnonce($annonce->getId());
            $Receveur = $managerEtudiant->findByAnnonce($annonce->getId());

        }
        $tableau = $managerNote->findByUser(1);

        if ($managerNote->findByUsers($Auteur->getId(),$Receveur->getId())){
            //déjà noté
            header("Location: index.php?controleur=note&methode=afficher");
            exit();
        }
      
        echo $template->render('pageSaisieDeNote.html.twig', [
            'role' => $role,
            'auteur' => $Auteur,
            'receveur' => $Receveur,
            'annonce' => $annonce
            //'annonces' => $annonces
        ]);


    }


    public function insererNote(){
        $template = $this->getTwig();
        $valeurNote = $_POST['noteVal'];
        $Auteur = $_POST['Auteur'];
        $Receveur = $_POST['Receveur'];
        $commentaire = $_POST['commentaire'] ?? '';
        $Annonce = $_POST['Annonce']; // À adapter selon le contexte

        
        //création objet Utilisateur pour auteur et receveur
        $managerUtilisateur = new UtilisateurDAO($this->getPdo());
        $Auteur = $managerUtilisateur->findById($Auteur);
        $Receveur = $managerUtilisateur->findById($Receveur);

        if($Auteur->getId()==1){echo "oui";}
        //création objet Annonce
        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $Annonce = $managerAnnonce->find($Annonce);

        //création de la note
        $note = new Note(
            null,
            $valeurNote,
            $commentaire,
            $Auteur,
            $Receveur,
            $Annonce
        );

        $managerNote = new NoteDao($this->getPdo());
        $managerNote->insert($note);

        header("Location: index.php?controleur=note&methode=afficher");
        exit();
    }

}