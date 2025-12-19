<?php
require_once "include.php";

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }
    
    /*==============================
     *
     *  Pous se connecter à la page 
     *  de connexion
     * 
     ===============================*/
    public function pageConnexion(){
        //En cas d'erreur 
        if(isset($_SESSION['msg_erreur'])){
            echo "<p style='color: red;'>".$_SESSION['msg_erreur']."</p>";
            unset($_SESSION['msg_erreur']);
        }

        $template = $this->getTwig();
        echo $template->render('pageDeConnexion.html.twig', [
            'user' => Utilisateur::getUser()
        ]);
    }

    /*==============================
     *
     *  Pous se connecter à la page 
     *  d'inscription
     * 
     ===============================*/
    public function pageInscription(){
        //En cas d'erreur 
        if(isset($_SESSION['msg_erreur'])){
            echo "<p style='color: red;'>".$_SESSION['msg_erreur']."</p>";
            unset($_SESSION['msg_erreur']);
        }
        if(isset($_SESSION['id'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['id'];
        }

        $template = $this->getTwig();

        echo $template->render('pageInscription.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    /*=========================================
     *
     *  Permet d'inscrire les données de
     *  l'utilisateur dans la base de données
     *  tout en chiffrant le mot de passe
     * 
     =========================================*/
    public function inscriptionBd(Utilisateur $user){

        // Vérifie si le mot de passe est robuste
        if (!Valide::estRobuste($user->getMdp()))
        {
            throw new Exception("mdp_faible");
        }

        // Vérifie si l'email existe déjà
        if (Valide::emailExiste($user->getEmail()))
        {
            throw new Exception("compte_existant");
        }

        // Obtention de l'instance PDO
        $baseDeDonnees = Bd::getInstance();

        // Hachage du mot de passe
        $passwordHache = password_hash($user->getMdp(), PASSWORD_BCRYPT);

        // Requête d'insertion
        $pdo = $baseDeDonnees->getConnexion();


        //verification type d'utilisateur
        if (get_class($user) === 'Etudiant'){
            $managerEtudiant = new EtudiantDao($pdo);
            $managerEtudiant->insererUtilisateur($user, $passwordHache);
           
        }
        else{
            $managerParticulier = new ParticulierDao($pdo);
            $managerParticulier->insererUtilisateur($user, $passwordHache);
        }

    }

    /*========================================
     *
     *  Permet de récupérer les informations
     *  de l'utilisateur depuis le formulaire
     *  et les inscrits dans la BD
     * 
     =========================================*/
    public function inscription(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Récupération des données envoyées par le formulaire
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $dateNaiss = $_POST['datenaiss'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $role = $_POST['role'] ?? '';
            $codeINE = $_POST['codeINE'] ?? '';
            $ville = $_POST['ville'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $codePostal = $_POST['codePostal'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($role == 'Etudiant') {
                $user = new Etudiant(null, $codeINE, $nom,  $prenom, $phone, $dateNaiss, $role, $email, $password, $adresse, $ville, $codePostal, null);

            } else {
                $user = new Particulier(null, $nom, $prenom, $phone, $dateNaiss, $role, $email, $password, $adresse, $ville, $codePostal, null);
            }           

            try
            {
                // Tentative d'inscription
                $this->inscriptionBd($user);

                // Si l'utilisateur a pu être inscrit en BD, affichage de la page de connexion
                header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                exit();
            }
            //sinon affiche des messages d'erreurs selon le probleme
            catch (Exception $e)
            {
                switch ($e->getMessage())
                {
                    case "compte_existant":
                        $_SESSION['msg_erreur']="Ce compte existe déjà.<a href='#'>Mot de passe oublié ?";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();

                    case "mdp_faible":
                        $_SESSION['msg_erreur']="Erreur : Mot de passe invalide." . $user->getId() . $user->getNom() . $user->getPrenom() . $user->getTel() . $user->getDateNaiss() . $user->getEmail() . $user->getMdp() . $user->getAdresse() . $user->getVille() . $user->getCodePostal() . $user->getDateSuppression() . $email."
                        Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();                        

                    default:
                        $_SESSION['msg_erreur']="Une erreur inattendue est survenue : {$e->getMessage()}";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        exit();
                }
            }
        }
    }
    
    /*=======================================
     *
     *  Réinitialise les tentatives échouées 
     * après une authentification réussie
     *
     =======================================*/
    public function reinitialiserTentativesConnexion(Utilisateur $user):void{
        // Remet à zéro les tentatives échouées
        $user->setTentativesEchouees(0);
        $user->setDateDernierEchecConnexion(null);

        // Mise à jour dans la base de données
        $bd=Bd::getInstance()->getConnexion();
        $requete=$bd->prepare('UPDATE Utilisateur 
                           SET tentativesEchouees = 0, 
                               dateDernierEchecConnexion = NULL 
                           WHERE id = :id');
        $requete->execute(['id'=>$user->getId()]);

    }

    /*=======================================
     *
     *  Calcul le temps restant avant que le 
     *  compte soit débloqué
     *
     =======================================*/
    public function tempsRestantAvantDeblocage(Utilisateur $user):int{
        $constantesConnexion = Constantes::getConstantes()['tentative'];
        if(!$user->getDateDernierEchecConnexion()){
            // Si aucune tentative échouée n'a été enregistrée
            return 0;
        }
        $dernierEchecTimestamp = strtotime($user->getDateDernierEchecConnexion());
        $tempsEcoule = time() - $dernierEchecTimestamp;
        $tempsRestant = $constantesConnexion['DELAI_ATTENTE_CONNEXION'] -$tempsEcoule;
        return $tempsRestant > 0 ? $tempsRestant : 0;
    }

    /*=======================================
     *
     *  Réactive le compte une fois que le 
     *  délai soit écoulé
     *
     =======================================*/
     public function reactiverCompte(Utilisateur $user):void{
        //Mise à jour des attributs de l'utilisateur
        $user->setTentativesEchouees(0);
        $user->setDateDernierEchecConnexion(null);
        $user->setStatutCompte('actif');

        // Mise à jour dans la base de données
        $bd=Bd::getInstance()->getConnexion();
        $requete=$bd->prepare('UPDATE Utilisateur 
                               SET tentativesEchouees = 0, 
                                   dateDernierEchecConnexion = NULL, 
                                   statutCompte = "actif" 
                               WHERE id = :id');   
        $requete->execute(['id'=>$user->getId()]);
     }

     /*=======================================
     *
     *  Gère les échecs de connexion,
     *  incrémente le nombre de tentative échouée
     *  et désactive le compte si le nombre de tentatives 
     *  est supérieur au maximum autorisé (3)
     *
     =======================================*/
     public function gererEchecConnexion(Utilisateur $user):void{
        $constantesConnexion = Constantes::getConstantes()['tentative'];
        $nbTentative=$user->getTentativesEchouees() + 1;
        $user->setTentativesEchouees($nbTentative);

        $bd=Bd::getInstance()->getConnexion();

        if($user->getTentativesEchouees() >= $constantesConnexion['MAX_CONNEXIONS_ECHOUEES']){
            // Désactivation du compte
            $requete = $bd->prepare(
                'UPDATE Utilisateur 
                 SET tentativesEchouees = :tentatives, 
                 dateDernierEchecConnexion = NOW(), 
                 statutCompte = "desactive" 
                 WHERE id = :id'
            );
            $user->setStatutCompte('desactive');
            $exception="nombre_tentative_depasse";
        }
        else{
            // Mise à jour des tentatives échouées
            $requete = $bd->prepare(
                'UPDATE Utilisateur 
                 SET tentativesEchouees = :tentatives, 
                 dateDernierEchecConnexion = NOW() 
                 WHERE id = :id'
            );
            $exception="mdp_invalide";
        }
        $requete->execute([
            'tentatives' => $nbTentative,
            'id' => $user->getId()
        ]);
        throw new Exception($exception);
     }

    /*=======================================
     *
     *  Vérifie si les identifiants récupérés
     *  correspondent à ceux de la base 
     *  de données
     *
     =======================================*/
    public function authentification(Utilisateur $user):bool{
        // création d'une instance de la bd
        $baseDeDonnees = Bd::getInstance();

        $pdo = $baseDeDonnees->getConnexion();

        // Recherche de l'utilisateur
        $requete= $pdo->prepare(
            'SELECT id, mdp, tentativesEchouees, dateDernierEchecConnexion, statutCompte,
             role FROM Utilisateur WHERE email =:email;'
        );

        // Exécution de la requête avec l'email de l'utilisateur
        $requete->execute(['email' => $user->getEmail()]);
        // Récupération des infos de l'utilisateur
        $donneeUtilisateurEnBD = $requete->fetch(PDO::FETCH_ASSOC);
        // Vérifie si l'utilisateur en BD existe
        if(!$donneeUtilisateurEnBD){
            throw new Exception("mail_invalide");
        }
        
        // Hydrate l'objet utilisateur avec les données récupérées
        $user->setId($donneeUtilisateurEnBD['id']);
        $user->setTentativesEchouees($donneeUtilisateurEnBD['tentativesEchouees']);
        $user->setDateDernierEchecConnexion($donneeUtilisateurEnBD['dateDernierEchecConnexion']);
        $user->setStatutCompte($donneeUtilisateurEnBD['statutCompte']);

        // Vérification du statut du compte
        if($user->getStatutCompte() === 'desactive'){
            if($this->tempsRestantAvantDeblocage($user)!==0){
                throw new Exception("compte_desactive");
            }
            $this->reactiverCompte($user);
        }

        // Vérification du mot de passe avec la fonction password_verify
        if(password_verify($user->getMdp(), $donneeUtilisateurEnBD['mdp'])){
            // throw new Exception("mdp_invalide");
            if($user->getTentativesEchouees() > 0){
                $this->reinitialiserTentativesConnexion($user);
            }
            $_SESSION['id'] = $user->getId();
            return true; // Authentification réussie
        }
        $this->gererEchecConnexion($user);
        return false; // Authentification échoué
    }

    /*==============================
     *
     *  Récupère les informations de connexions
     *  de l'utilisateur, vérifie
     *  s'ils sont valides et 
     *  affiche la page d'accueil
     *  selon le role de l'utilisateur
     *  (particulier - étudiant)
     * 
     ===============================*/
    public function connexion(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Récupération des données du formulaire
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['mdp'] ?? '';

            //Création d'une instance utilisateur avec les données récupérés
            $utilisateur = new Utilisateur(null, null, null, null, null, null, $email, $mdp);

            try{
                //Tentative de connexion
                if($this->authentification($utilisateur)){
                    header("Location: index.php");
                    exit();
                }
            }
            catch (Exception $e){
                switch($e->getMessage()){
                    case "mdp_invalide":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur']="L'email ou le mot de passe est incorrect";
                        exit();
                    case "mail_invalide":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur']="L'email est incorrect";
                        exit();       
                    case "nombre_tentative_depasse":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur']="Trop de tentatives de connexion, le compte à été bloqué pour ".$this->tempsRestantAvantDeblocage($utilisateur);
                        exit();   
                    case "compte_desactive":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur']="Trop de tentatives de connexion, le compte a été bloqué pour ".$this->tempsRestantAvantDeblocage($utilisateur)." secondes";
                        exit();   
                    default:
                        $_SESSION['msg_erreur']="Une erreur inattendue est survenue : {$e->getMessage()}";
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        exit();

                }
            }
        }
    }


    /*==============================
     *
     *  Affiche les informations du
     *  du compte de l'utilisateur
     * 
     ===============================*/
    public function afficheCompte(){
        $template = $this->getTwig();

        echo $template->render('pageCompte.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    /*==============================
     *
     *  Affiche la page de 
     * modification du compte
     * 
     ===============================*/
    public function pageModifierCompte(){
        $template = $this->getTwig();

        echo $template->render('pageModifierCompte.html.twig', [
            'user' => Utilisateur::getUser(),
            'err' => "",
        ]);
    }

    /*==============================
     *
     *  Se déconnecte et affiche
     *  la page d'accueil
     * 
     ===============================*/
    public function deconnexion(): void{
        $_SESSION=[]; // On vide le tableau, pour libérer de l'espace
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public function modiferCompte(){
        $currentUser = Utilisateur::getUser();
        $template = $this->getTwig();

        $nom = $_POST['nom'];
        if ($nom != $currentUser->getNom()) {
            /* Vérifier nom */
        }
        $prenom = $_POST['prenom'];
        if ($prenom != $currentUser->getPrenom()) {
            /* Vérifier prenom */
        }
        $dateNaiss = $_POST['dateNaiss'];
        if ($dateNaiss != $currentUser->getDateNaiss()) {
            /* Vérifier dateNaiss */
        }
        $email = $_POST['email'];
        if ($email != $currentUser->getEmail()) {
            if (Valide::emailExiste($email)) {
                echo $template->render('pageModifierCompte.html.twig', [
                    'user' => Utilisateur::getUser(),
                    'errEmail' => "Email invalide",
                ]);
                exit;
            }
        }
        $tel = $_POST['tel'];
        if ($tel != $currentUser->getTel()) {
            /* Vérifier téléphone */
        }
        $ville = $_POST['ville'];
        if ($ville != $currentUser->getVille()) {
            /* Vérifier ville */
        }
        $adresse = $_POST['adresse'];
        if ($adresse != $currentUser->getAdresse()) {
            /* Vérifier adresse */
        }
        $codePostal = $_POST['codePostal'];
        if ($codePostal != $currentUser->getCodePostal()) {
            /* Vérifier codePostal */
        }
        /*$codeINE = $_POST['codeINE'];
        if ($codeINE != $currentUser->getCodeINE()) {
        }*/

        $mdp = $_POST['mdp'];
        if ($mdp != "") {
            /* Vérifier mdp */ 
        }

    

        
        echo $template->render('pageCompte.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }
}
