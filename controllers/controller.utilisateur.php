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

        echo $template->render('pageInscription.html.twig', [
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

    public function inscriptionBd(string $nom, string $prenom, string $email, string $password){
        // Vérifie si le mot de passe est robuste
        if (!Valide::estRobuste($password))
        {
            throw new Exception("mdp_faible");
        }

        // Vérifie si l'email existe déjà
        if (Valide::emailExiste($email))
        {
            throw new Exception("compte_existant");
        }

        // Obtention de l'instance PDO
        $baseDeDonnees = Bd::getInstance();

        // Hachage du mot de passe
        $passwordHache = password_hash($password, PASSWORD_BCRYPT);

        // Préparation de la requête d'insertion
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'INSERT INTO Utilisateur (id, role, nom, prenom, dateNaiss, email, mdp) VALUES ("005", "Etudiant", "nom", "prenom", "2006-11-20", :email, :password)'
        );

        // Exécution de la requête
        $requete->execute([
            'email' => $email,
            'password' => $passwordHache,
        ]);
    }


    public function inscription(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Récupération des données envoyées par le formulaire
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            try
            {
                // Tentative d'inscription
                $this->inscriptionBd($nom, $prenom, $email, $password);

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
                        echo '<a href="index.php?controleur=utilisateur&methode=pageConnexion">Retour au formulaire d\'inscription</a>';
                        break;

                    case "mdp_faible":
                        echo '<h1>Erreur : Mot de passe invalide</h1>';
                        echo '<p>Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.</p>';
                        echo '<a href="index.php?controleur=utilisateur&methode=pageConnexion">Retour au formulaire d\'inscription</a>';
                        break;

                    default:
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        echo "<p>{$e->getMessage()}</p>";
                        echo '<a href="index.php?controleur=utilisateur&methode=pageConnexion">Retour au formulaire d\'inscription</a>';
                        break;
                }
            }
        }
    }
}
