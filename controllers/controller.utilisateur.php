<?php
require_once "include.php";
class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }
    public function pageConnexion(){
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

    public function pageInscription(){
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

                // Si l'utilisateur a pu être inscrit en BD, affichage du succès
                echo "<h1>Inscription réussie !</h1>";
                echo '<a href="index.php?controleur=utilisateur&methode=pageConnexion">Se connecter</a>';
            }
            catch (Exception $e)
            {
                switch ($e->getMessage())
                {
                    case "compte_existant":
                        echo '<h1>Erreur : Compte existant</h1>';
                        echo '<p>Ce compte existe déjà.</p>';
                        echo '<a href="#">Mot de passe oublié ?</a><br>';
                        echo '<a href="index.php?controleur=utilisateur&methode=pageInscription">Retour au formulaire d\'inscription</a>';
                        break;

                    case "mdp_faible":
                        echo '<h1>Erreur : Mot de passe invalide</h1>';
                        echo '<p>Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.</p>';
                        echo '<a href="index.php?controleur=utilisateur&methode=pageInscription">Retour au formulaire d\'inscription</a>';
                        echo $user->getMdp();
                        break;

                    default:
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        echo "<p>{$e->getMessage()}</p>";
                        echo '<a href="index.php?controleur=utilisateur&methode=pageInscription">Retour au formulaire d\'inscription</a>';
                        break;
                }
            }
        }
    }

    //Authentifie un utilisateur
    public function authentification(Utilisateur $user):bool{
        // création d'une instance de la bd
        $baseDeDonnees = Bd::getInstance();

        // Recherche de l'utilisateur
        $requete=$baseDeDonnees->prepare(
            'SELECT id, mdp FROM utilisateur WHERE email =:email'
        );

        // Exécution de la requête avec l'email de l'utilisateur
        $requete->execute(['email' => $user->getEmail()]);

        // Récupération des info de l'utilisateur
        $donneeUtilisateurEnBD=$requete->fetch(PDO::FETCH_ASSOC);

        // Vérifie si l'utilisateur en BD existe
        if($donneeUtilisateurEnBD){
            // Vérification du mot de passe avec la fonction password_verify
            if(password_verify($user->getMdp(), $donneeUtilisateurEnBD['mdp'])){
                // Synchronisation de l'identifiant récupéré de la base de données avec l'objet courant
                $user->setId($donneeUtilisateurEnBD['id']);

                // Réinitialisation du mot de passe pour éviter de conserver des données sensibles
                $user->setMdp('');

                return true; // Authentification réussie
            }
        }
        return false; // Authentification échouée
    }

    /*Appeler depuis pageDeConnexion.html.twig, permet de se connecter en appelant la méthode authentification
     *
     * 
     * 
     */
    public function connexion(){
        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            //Récupération des données du formulaire
            $email = $_POST['email']??'';
            $mdp = $_POST['mdp']??'';

            //Création d'une instance utilisateur avec les données récupérés
            $utilisateur = new Utilisateur($email,$mdp);

            try{
                //Tentative de connexion
                if($this->authentification($utilisateur)){
                    echo "Connexion réussie.";
                }
                else{
                    echo "Erreur : Email ou mot de passe incorrect.";
                    echo '<br><a href="index.php?controleur=utilisateur&methode=pageConnexion">Retourner à la page de connexion</a>';
                }
                return true;
            }
            catch (Exception $e){
                switch($e ->getMessage()){
                    case "email_ou_mdp_incorrect":

                }
            }
        }
    }
}
