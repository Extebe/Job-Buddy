<?php

class ControllerAnnonce extends Controller {
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
        $managerAnnonce = new AnnonceDao($this->getPdo());
        $tableau = $managerAnnonce->findAllAssoc();
        $annonces = $managerAnnonce->hydrateAll($tableau);

        echo $template->render('index.html.twig', [
            'role' => $role,
            'annonces' => $annonces
            //'annonces' => $annonces
        ]);


    }

    public function listerAnnonces(){

    }

}