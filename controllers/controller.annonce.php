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
        echo $template->render('index.html.twig', [
            'user' => Utilisateur::getUser(),
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
            'role' => $role
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

}