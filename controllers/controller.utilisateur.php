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
        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        $template = $this->getTwig();

        echo $template->render('pageDeConnexion.html.twig', [
            'role' => $role
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
        if(isset($_SESSION['role'])){
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        }
        else{
            $role = "non_connecte";
        }

        $template = $this->getTwig();

        echo $template->render('pageInscription.html.twig', [
            'role' => $role
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
        $utilisateurDao = new UtilisateurDao($pdo);
        $utilisateurDao->insererUtilisateur($user, $passwordHache);
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

            $user = new Utilisateur(null, $nom, $prenom, $phone, $dateNaiss, $role, $codeINE, $email, $password, $adresse, $ville, $codePostal);
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
                        break;

                    case "mdp_faible":
                        $_SESSION['msg_erreur']="Erreur : Mot de passe invalide. 
                        Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();
                        break;
                        

                    default:
                        $_SESSION['msg_erreur']="Une erreur inattendue est survenue : {$e->getMessage()}";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        exit();
                        break;
                }
            }
        }
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
            'SELECT id, mdp, role FROM Utilisateur WHERE email =:email'
        );

        // Exécution de la requête avec l'email de l'utilisateur
        $requete->execute(['email' => $user->getEmail()]);

        // Récupération des info de l'utilisateur
        $donneeUtilisateurEnBD = $requete->fetch(PDO::FETCH_ASSOC);
        // Vérifie si l'utilisateur en BD existe
        if($donneeUtilisateurEnBD){
            // Vérification du mot de passe avec la fonction password_verify
            if(password_verify($user->getMdp(), $donneeUtilisateurEnBD['mdp'])){
                // Synchronisation de l'identifiant récupéré de la base de données avec l'objet courant
                $user->setId($donneeUtilisateurEnBD['id']);

                // Réinitialisation du mot de passe pour éviter de conserver des données sensibles
                $user->setMdp('');
                $_SESSION['role'] = $donneeUtilisateurEnBD['role'];
                return true; // Authentification réussie
            }
            throw new Exception("identifiant_invalide");
        }
        return false; // Authentification échouée
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
            $utilisateur = new Utilisateur(null, null, null, null, null, null, null, $email,$mdp);

            try{
                //Tentative de connexion
                if($this->authentification($utilisateur)){
                    header("Location: index.php");
                    exit();
                }
            }
            catch (Exception $e){
                switch($e ->getMessage()){
                    case "identifiant_invalide":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur']="L'email ou le mot de passe est incorrect";
                        exit();
                        break;                     
                }
            }
        }
    }
}
