<?php
require_once "include.php";

/**
 * @brief Contrôleur gérant les utilisateurs (connexion, inscription, compte).
 */
class ControllerUtilisateur extends Controller
{
    /**
     * @brief Constructeur du contrôleur utilisateur.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la page de connexion.
     */
    public function pageConnexion()
    {
        //En cas d'erreur 
        if (isset($_SESSION['msg_erreur'])) {
            echo "<p style='color: red;'>" . $_SESSION['msg_erreur'] . "</p>";
            unset($_SESSION['msg_erreur']);
        }

        $template = $this->getTwig();
        echo $template->render('pageDeConnexion.html.twig', [
            'user' => Utilisateur::getUser()
        ]);
    }

    /**
     * @brief Affiche la page d'inscription.
     */
    public function pageInscription()
    {
        //En cas d'erreur 
        if (isset($_SESSION['msg_erreur'])) {
            echo "<p style='color: red;'>" . $_SESSION['msg_erreur'] . "</p>";
            unset($_SESSION['msg_erreur']);
        }
        if (isset($_SESSION['id'])) {
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['id'];
        }

        $template = $this->getTwig();

        echo $template->render('pageInscription.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    /**
     * @brief Inscrit un utilisateur dans la base de données.
     * @param Utilisateur $user L'objet utilisateur à inscrire.
     * @throws Exception Si le MDP est faible ou l'email existe déjà.
     */
    public function inscriptionBd(Utilisateur $user)
    {

        // Vérifie si le mot de passe est robuste
        if (!Valide::estRobuste($user->getMdp())) {
            throw new Exception("mdp_faible");
        }

        // Vérifie si l'email existe déjà
        if (Valide::emailExiste($user->getEmail())) {
            throw new Exception("compte_existant");
        }

        // Hachage du mot de passe
        $passwordHache = password_hash($user->getMdp(), PASSWORD_BCRYPT);

        // Obtention de l'instance PDO
        $pdo = $this->getPdo();


        //verification type d'utilisateur
        if (get_class($user) === 'Etudiant') {
            $managerEtudiant = new EtudiantDao($pdo);
            $managerEtudiant->insererUtilisateur($user, $passwordHache);

        } else {
            $managerParticulier = new ParticulierDao($pdo);
            $managerParticulier->insererUtilisateur($user, $passwordHache);
        }

    }

    /**
     * @brief Gère la soumission du form d'inscription.
     */
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $cvec = $_POST['cvec'] ?? '';

            if ($role == 'Etudiant') {
                $user = new Etudiant($id = null, $codeINE, $nom, $prenom, $phone, $dateNaiss, $role, $email, $password, $adresse, $ville, $codePostal, null, $cvec);

            } else {
                $user = new Particulier(null, $nom, $prenom, $phone, $dateNaiss, $role, $email, $password, $adresse, $ville, $codePostal, null);
            }

            try {
                // Tentative d'inscription
                $this->inscriptionBd($user);

                // Si l'utilisateur a pu être inscrit en BD, affichage de la page de connexion
                header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                exit();
            }
            //sinon affiche des messages d'erreurs selon le probleme
            catch (Exception $e) {
                switch ($e->getMessage()) {
                    case "compte_existant":
                        $_SESSION['msg_erreur'] = "Ce compte existe déjà.<a href='#'>Mot de passe oublié ?";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();

                    case "mdp_faible":
                        $_SESSION['msg_erreur'] = "Erreur : Mot de passe invalide." . $user->getId() . $user->getNom() . $user->getPrenom() . $user->getTel() . $user->getDateNaiss() . $user->getEmail() . $user->getMdp() . $user->getAdresse() . $user->getVille() . $user->getCodePostal() . $user->getDateSuppression() . $email . "
                        Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();

                    case "CVEC invalide":
                        $_SESSION['msg_erreur'] = "Erreur : CVEC invalide.";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();

                    default:
                        $_SESSION['msg_erreur'] = "Une erreur inattendue est survenue : {$e->getMessage()}";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        exit();
                }
            }
        }
    }

    /**
     * @brief Réinitialise le compteur de tentatives de connexion échouées.
     * @param Utilisateur $user L'utilisateur.
     */
    public function reinitialiserTentativesConnexion(Utilisateur $user): void
    {
        // Remet à zéro les tentatives échouées
        $user->setTentativesEchouees(0);
        $user->setDateDernierEchecConnexion(null);

        // Mise à jour dans la base de données
        $bd = $this->getPdo();
        $requete = $bd->prepare('UPDATE Utilisateur 
                           SET tentativesEchouees = 0, 
                               dateDernierEchecConnexion = NULL 
                           WHERE id = :id');
        $requete->execute(['id' => $user->getId()]);

    }

    /**
     * @brief Calcule le temps restant avant le déblocage du compte.
     * @param Utilisateur $user L'utilisateur.
     * @return int Temps restant en secondes.
     */
    public function tempsRestantAvantDeblocage(Utilisateur $user): int
    {
        $constantesConnexion = Constantes::getConstantes()['tentative'];
        if (!$user->getDateDernierEchecConnexion()) {
            // Si aucune tentative échouée n'a été enregistrée
            return 0;
        }
        $dernierEchecTimestamp = strtotime($user->getDateDernierEchecConnexion());
        $tempsEcoule = time() - $dernierEchecTimestamp;
        $tempsRestant = $constantesConnexion['DELAI_ATTENTE_CONNEXION'] - $tempsEcoule;
        return $tempsRestant > 0 ? $tempsRestant : 0;
    }

    /**
     * @brief Réactive le compte utilisateur.
     * @param Utilisateur $user L'utilisateur.
     */
    public function reactiverCompte(Utilisateur $user): void
    {
        //Mise à jour des attributs de l'utilisateur
        $user->setTentativesEchouees(0);
        $user->setDateDernierEchecConnexion(null);
        $user->setStatutCompte('actif');

        // Mise à jour dans la base de données
        $bd = $this->getPdo();
        $requete = $bd->prepare('UPDATE Utilisateur 
                               SET tentativesEchouees = 0, 
                                   dateDernierEchecConnexion = NULL, 
                                   statutCompte = "actif" 
                               WHERE id = :id');
        $requete->execute(['id' => $user->getId()]);
    }

    /**
     * @brief Gère un échec de connexion.
     * @param Utilisateur $user L'utilisateur concerné.
     * @throws Exception avec "nombre_tentative_depasse" ou "mdp_invalide".
     */
    public function gererEchecConnexion(Utilisateur $user): void
    {
        $constantesConnexion = Constantes::getConstantes()['tentative'];
        $nbTentative = $user->getTentativesEchouees() + 1;
        $user->setTentativesEchouees($nbTentative);

        $bd = $this->getPdo();

        if ($user->getTentativesEchouees() >= $constantesConnexion['MAX_CONNEXIONS_ECHOUEES']) {
            // Désactivation du compte
            $requete = $bd->prepare(
                'UPDATE Utilisateur 
                 SET tentativesEchouees = :tentatives, 
                 dateDernierEchecConnexion = NOW(), 
                 statutCompte = "desactive" 
                 WHERE id = :id'
            );
            $user->setStatutCompte('desactive');
            $exception = "nombre_tentative_depasse";
        } else {
            // Mise à jour des tentatives échouées
            $requete = $bd->prepare(
                'UPDATE Utilisateur 
                 SET tentativesEchouees = :tentatives, 
                 dateDernierEchecConnexion = NOW() 
                 WHERE id = :id'
            );
            $exception = "mdp_invalide";
        }
        $requete->execute([
            'tentatives' => $nbTentative,
            'id' => $user->getId()
        ]);
        throw new Exception($exception);
    }

    /**
     * @brief Vérifie les identifiants de l'utilisateur.
     * @param Utilisateur $user L'utilisateur (avec email et mdp).
     * @return bool True si authentification réussie, false sinon.
     * @throws Exception si erreur compte (email/compte bloqué).
     */
    public function authentification(Utilisateur $user): bool
    {
        // création d'une instance de la bd
        $pdo = $this->getPdo();

        // Recherche de l'utilisateur
        $requete = $pdo->prepare(
            'SELECT id, mdp, tentativesEchouees, dateDernierEchecConnexion, statutCompte,
             role FROM Utilisateur WHERE email =:email;'
        );

        // Exécution de la requête avec l'email de l'utilisateur
        $requete->execute(['email' => $user->getEmail()]);
        // Récupération des infos de l'utilisateur
        $donneeUtilisateurEnBD = $requete->fetch(PDO::FETCH_ASSOC);
        // Vérifie si l'utilisateur en BD existe
        if (!$donneeUtilisateurEnBD) {
            throw new Exception("mail_invalide");
        }

        // Hydrate l'objet utilisateur avec les données récupérées
        $user->setId($donneeUtilisateurEnBD['id']);
        $user->setTentativesEchouees($donneeUtilisateurEnBD['tentativesEchouees']);
        $user->setDateDernierEchecConnexion($donneeUtilisateurEnBD['dateDernierEchecConnexion']);
        $user->setStatutCompte($donneeUtilisateurEnBD['statutCompte']);

        // Vérification du statut du compte
        if ($user->getStatutCompte() === 'desactive') {
            if ($this->tempsRestantAvantDeblocage($user) !== 0) {
                throw new Exception("compte_desactive");
            }
            $this->reactiverCompte($user);
        }

        // Vérification du mot de passe avec la fonction password_verify
        if (password_verify($user->getMdp(), $donneeUtilisateurEnBD['mdp'])) {
            // throw new Exception("mdp_invalide");
            if ($user->getTentativesEchouees() > 0) {
                $this->reinitialiserTentativesConnexion($user);
            }
            $_SESSION['id'] = $user->getId();
            return true; // Authentification réussie
        }
        $this->gererEchecConnexion($user);
        return false; // Authentification échoué
    }

    /**
     * @brief Gère la tentative de connexion via le formulaire.
     */
    public function connexion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Récupération des données du formulaire
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['mdp'] ?? '';

            //Création d'une instance utilisateur avec les données récupérés
            $utilisateur = new Utilisateur(null, null, null, null, null, null, $email, $mdp);

            try {
                //Tentative de connexion
                if ($this->authentification($utilisateur)) {
                    header("Location: index.php");
                    exit();
                }
            } catch (Exception $e) {
                switch ($e->getMessage()) {
                    case "mdp_invalide":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur'] = "L'email ou le mot de passe est incorrect";
                        exit();
                    case "mail_invalide":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur'] = "L'email est incorrect";
                        exit();
                    case "nombre_tentative_depasse":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur'] = "Trop de tentatives de connexion, le compte à été bloqué pour " . $this->tempsRestantAvantDeblocage($utilisateur);
                        exit();
                    case "compte_desactive":
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        $_SESSION['msg_erreur'] = "Trop de tentatives de connexion, le compte a été bloqué pour " . $this->tempsRestantAvantDeblocage($utilisateur) . " secondes";
                        exit();
                    default:
                        $_SESSION['msg_erreur'] = "Une erreur inattendue est survenue : {$e->getMessage()}";
                        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
                        echo "<h1>Une erreur inattendue est survenue</h1>";
                        exit();

                }
            }
        }
    }


    /**
     * @brief Affiche les informations du compte de l'utilisateur.
     */
    public function afficheCompte()
    {
        if (!Utilisateur::getUser()) {
            header('Location: index.php');
            exit;
        }
        $template = $this->getTwig();

        echo $template->render('pageCompte.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    /**
     * @brief Affiche la page de modification du compte.
     */
    public function pageModifierCompte()
    {
        if (!Utilisateur::getUser()) {
            header('Location: index.php');
            exit;
        }
        $template = $this->getTwig();

        echo $template->render('pageModifierCompte.html.twig', [
            'user' => Utilisateur::getUser(),
            'err' => "",
        ]);
    }

    /**
     * @brief Déconnecte l'utilisateur.
     */
    public function deconnexion(): void
    {
        $_SESSION = []; // On vide le tableau, pour libérer de l'espace
        session_destroy();
        header('Location: index.php');
        exit();
    }

    /**
     * @brief Gère la modification du compte (TODO).
     */
    public function modiferCompte()
    {
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

    /**
     * @brief Inscrit l'utilisateur à la newsletter.
     */
    public function newsletter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $pdoNewsLetter = new NewLetterDao($this->getPdo());

            if (!$pdoNewsLetter->emailExisteNewsletter($email)) {
                $newsLetter = new NewLetter(null, $email);
                $pdoNewsLetter->insererEmail($newsLetter);
                echo "l'email a bien été enregistrer dans la base de données";
            }
            echo "Vous vous êtes déjà inscrit.";
            echo "<a href='index.php'> Retour à la page d'accueil</a>";
        }
    }

    /**
     * @brief Affiche la page d'administration.
     */
    public function admin()
    {
        $template = $this->getTwig();
        if (!Utilisateur::getUser() || Utilisateur::getUser()->getRole() !== 'Etudiant') {
            header('Location: index.php');
            exit;
        }

        echo $template->render('pageAdmin.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }
}
