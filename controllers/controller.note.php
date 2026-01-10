<?php

/**
 * @brief Contrôleur gérant les notes et avis.
 */
class ControllerNote extends Controller
{
    /**
     * @brief Constructeur du contrôleur note.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la page des notes.
     */
    public function afficher()
    {
        $template = $this->getTwig();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }
        //recupération des annonces
        $managerNote = new NoteDao($this->getPdo());
        $tableau = $managerNote->findByUser(Utilisateur::getUser()->getId());
        $notes = $managerNote->hydrateAll($tableau);

        echo $template->render('pageDeNote.html.twig', [
            'notes' => $notes,
            'user' => Utilisateur::getUser()
            //'annonces' => $annonces
        ]);


    }

    /**
     * @brief Affiche le formulaire de saisie de note.
     */
    public function saisieNote()
    {
        $template = $this->getTwig();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }
        $role = Utilisateur::getUser()->getRole();

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $annonce = $managerAnnonce->find($_GET['id']);
        $annonces = $managerAnnonce->findAllById(Utilisateur::getUser()->getId());

        $ids = [];

        foreach ($annonces as $t) {
            $ids[] = $t->getId();
        }



        if (!in_array($annonce->getId(), $ids)) {
            //on ne peut pas se noter soi-même
            header("Location: index.php?controleur=note&methode=afficher");
            exit();
        }
        //recupération des annonces
        $managerNote = new NoteDao($this->getPdo());
        $managerParticulier = new ParticulierDAO($this->getPdo());
        $managerEtudiant = new EtudiantDAO($this->getPdo());

        if ($role == 'Etudiant') {
            $Auteur = $managerEtudiant->findByAnnonce($annonce->getId());
            $Receveur = $managerParticulier->findByAnnonce($annonce->getId());
        } else {
            $Auteur = $managerParticulier->findByAnnonce($annonce->getId());
            $Receveur = $managerEtudiant->findByAnnonce($annonce->getId());

        }
        $idAuteur = $Auteur->getId();
        $idReceveur = $Receveur->getId();
        if ($managerNote->findByUsers($idAuteur, $idReceveur)->getId() != null) {
            //déjà noté
            header("Location: index.php?controleur=note&methode=afficher");
            exit();
        }
        echo $managerNote->findByUsers($idAuteur, $idReceveur);

        echo $template->render('pageSaisieDeNote.html.twig', [
            'auteur' => $Auteur,
            'receveur' => $Receveur,
            'annonce' => $annonce
            ,
            'user' => Utilisateur::getUser()
            //'annonces' => $annonces
        ]);


    }


    /**
     * @brief Insère une nouvelle note dans la base de données.
     */
    public function insererNote()
    {
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