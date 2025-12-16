<?php

class ControllerParticulier extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function pageAjouterAnnonce() {
        $template = $this->getTwig();

        echo $template->render('ajouterAnnonce.html.twig');
    }
    public function ajouterAnnonce(){

    }
}