<?php

class ControllerAnnonce extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        $template = $this->getTwig();

        $managerAnnonce = new AnnonceDao($this->getPdo());

        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        $annonces = $managerAnnonce->find("A001");

        echo $template->render('index.html.twig', [
            'role' => $role,
            'annonces' => $annonces // Si tu veux aussi passer les annonces au template
        ]);
    }


}