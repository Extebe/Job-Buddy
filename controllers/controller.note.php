<?php

class ControllerNote extends Controller {
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
        $managerNote = new NoteDao($this->getPdo());
        $tableau = $managerNote->findByUser('1');
        $notes = $managerNote->hydrateAll($tableau);
      
        echo $template->render('pageDeNote.html.twig', [
            'role' => $role,
            'notes' => $notes
            //'annonces' => $annonces
        ]);


    }

    public function saisieNote(){
        $template = $this->getTwig();

        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        //recupération des annonces
        $managerNote = new NoteDao($this->getPdo());
        $managerParticulier = new ParticulierDAO($this->getPdo());
        $managerEtudiant = new EtudiantDAO($this->getPdo());

        if ($role == 'etudiant'){
            $Auteur = $managerEtudiant->findByAnnonce(1);
            $Receveur = $managerParticulier->findByAnnonce(1);

        }
        else{
            $Auteur = $managerParticulier->findByAnnonce(1);
            $Receveur = $managerEtudiant->findByAnnonce(1);
            
        }
        $tableau = $managerNote->findByUser(1);
        $notes = $managerNote->hydrateAll($tableau);

      
        echo $template->render('pageSaisieDeNote.html.twig', [
            'role' => $role,
            'notes' => $notes,
            'auteur' => $Auteur,
            'receveur' => $Receveur
            //'annonces' => $annonces
        ]);


    }

}