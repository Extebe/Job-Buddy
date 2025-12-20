<?php

class ControllerAnnonce extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        $template = $this->getTwig();

        //recupération des annonces
        $managerAnnonce = new AnnonceDao($this->getPdo());
        $tableau = $managerAnnonce->findAllAssoc();
        $annonces = $managerAnnonce->hydrateAll($tableau);
        $icons = Constantes::getConstantes()['icons'];

        echo $template->render('index.html.twig', [
            'annonces' => $annonces,
            'icons' => $icons,
            'user' => Utilisateur::getUser()
        ]);


    }

    public function afficheFormAnnonce() {

        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        $template = $this->getTwig();

        echo $template->render('ajouterAnnonce.html.twig', [
            'role' => $role,
            'user' => Utilisateur::getUser()

        ]);
    }

    public function traiteFormAnnonce() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $titre = $_POST['titre'];
            $typeService = $_POST['typeService'];
            $dateDebut = $_POST['dateDebut'];
            $dateFin = $_POST['dateFin'];
            $description = $_POST['description'];

            //Appel de la requête qui créé l'annonce

        }
    }


    public function listerAnnonces(){

    }


    public function afficherMesAnnonces(){
        $template = $this->getTwig();


        $managerAnnonce = new AnnonceDao($this->getPdo());
        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }
        $tableau = $managerAnnonce->findAllById(Utilisateur::getUser()->getId());


        echo $template->render('mesAnnonces.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonces' => $tableau,
            'icons' => Constantes::getConstantes()['icons']
        ]);

    }


    public function afficherDetail(){
        $template = $this->getTwig();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }


        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDao($this->getPdo());
        $annonce = $managerAnnonce->find($idAnnonce);
        $annonce = $managerAnnonce->addRelations($annonce);

 
        echo $template->render('detailAnnonce.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonce' => $annonce,
            'icons' => Constantes::getConstantes()['icons']
        ]);
    }

    public function postulerAnnonce(){
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->postuler($idAnnonce, Utilisateur::getUser()->getId());

        header("Location: index.php?controleur=annonce&methode=afficherDetail&id=".$idAnnonce);
        exit();
    }

    public function supprimerAnnonce(){
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->supprimer($idAnnonce, Utilisateur::getUser()->getId());

        header("Location: index.php?controleur=annonce&methode=afficherMesAnnonces");
        exit();
    }
}