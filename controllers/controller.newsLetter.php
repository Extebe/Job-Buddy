<?php
    class ControllerNewsLetter extends Controller{
        public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }
    
    public function afficher(){
        $template=$this->getTwig();
        echo $template->render('inscriptionNewsLetter.html.twig',['user'=>Utilisateur::getUser()]);
    }
    public function afficherPolitiqueConfidentialite(){
        $template=$this->getTwig();
        echo $template->render('politiqueConfidentialite.html.twig',['user'=>Utilisateur::getUser()]);
    }
    }