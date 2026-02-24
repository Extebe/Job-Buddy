<?php

/**
 * @brief Contrôleur gérant les signalements.
 */
class ControllerSignalement extends Controller
{
    /**
     * @brief Constructeur du contrôleur Signalement.
     * @param \Twig\Environment $twig Environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la page pour signaler une annonce.
     */
    public function signalerAnnonce()
    {
        $template = $this->getTwig();
        $idAnnonce=$_GET['idAnnonce'];

        //recupération des annonces
        // $managerAnnonce = new AnnonceDao($this->getPdo());
        // $annonce = $managerAnnonce->findById($idUtilisateur,$idAnnonce);
        // $annonce = $managerAnnonce->hydrate($annonce);

        echo $template->render('pageDeSignalement.html.twig', [
            'user' => Utilisateur::getUser(),
            'idAnnonce' => $idAnnonce,
            'signaleur'=> Utilisateur::getUser()
        ]);
    }

    /**
     * @brief Ajoute à la base de données le signalement
     */
    public function ajouterBd()
    {
        $signaleur = Utilisateur::getUser();
        if(!$signaleur){
            header('Location: index.php?controleur=utilisateur&methode=pageConnexion');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $idAnnonce = $_POST['idAnnonce'];
            $idSignaleur = $signaleur->getId();
            $motif = $_POST['motif'];
            $description = $_POST['description'];

            $managerSignalement= new SignalementDao($this->getPdo());
            $signalement = $managerSignalement->updateSignalementAnnonce($motif,$description,$idSignaleur,$idAnnonce);
        }        
    }

    /**
     * @brief Affiche la page pour signaler un utilisateur.
     */
    public function signalerUtilisateur()
    {
        $template = $this->getTwig();
        $idOther=$_GET['idOther'];

        echo $template->render('pageDeSignalementUtilisateur.html.twig', [
            'user' => Utilisateur::getUser(),    
            'idOther' => $idOther,
            'signaleur' => Utilisateur::getUser()
        ]);
    }

    /**
     * @brief Ajoute à la base de données le signalement
     */
    public function ajouterBdSignalementUtilisateur()
    {
        $signaleur = Utilisateur::getUser();
        if(!$signaleur){
            header('Location: index.php?controleur=utilisateur&methode=pageConnexion');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $idOther = $_POST['idOther'];
            $idSignaleur = $signaleur->getId();
            $motif = $_POST['motif'];
            $description = $_POST['description'];

            $managerSignalement= new SignalementDao($this->getPdo());
            $signalement = $managerSignalement->updateSignalementUtilisateur($motif,$description,$idSignaleur,$idOther);
        }        
    }
}