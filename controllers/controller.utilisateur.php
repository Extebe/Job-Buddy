<?php

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }
    public function pageConnexion(){
        $template = $this->getTwig();

        echo $template->render('PageDeConnexion.html.twig');
    }

    public function pageInscription(){
        $template = $this->getTwig();

        echo $template->render('PageInscription.html.twig');
    }
}