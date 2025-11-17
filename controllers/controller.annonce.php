<?php

class ControllerAnnonce extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        $template = $this->getTwig();

        // $managerAnnonce = new AnnonceDao($this->getPdo());

        // $annonces = $managerAnnonce->findAll();

        echo $template->render('index.html.twig');
    }
}