<?php

class ControllerNote extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        $template = $this->getTwig()->load('noteUtilisateur.html.twig');

        echo $template->render(array('utilisateur' => $_SESSION['utilisateur'], 'noteUtilisateur' => $this->getModel('noteUtilisateur')->findAll()));
    }
}