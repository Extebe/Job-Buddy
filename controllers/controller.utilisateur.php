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

    //Appeler depuis pageDeConnexion.html.twig
    public function connexion(){
        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            //Récupération des données du formulaire
            $email = $_POST['email']??'';
            $mdp = $_POST['mdp']??'';

            //Création d'une instance utilisateur avec les données récupérés
            $utilisateur = new Utilisateur($email,$mdp);

            try{
                //Tentative de connexion
                return true;
            }
            catch (Exception $e){}
        }
    }
}
