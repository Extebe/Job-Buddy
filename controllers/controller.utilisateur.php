<?php
require_once "include.php";

/**
 * @file    controller.utilisateur.php
 * 
 * @brief
 */

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     *  Pous se connecter à la page 
     *  de connexion
     * @return void
     */
    public function pageConnexion()
    {
        $msg_erreur = null; // s'il n'y a pas d'erreur, on met la variable à null
        //En cas d'erreur 
        if (isset($_SESSION['msg_erreur'])) {
            $msg_erreur = $_SESSION['msg_erreur'];
            unset($_SESSION['msg_erreur']);
        }

        $template = $this->getTwig();
        echo $template->render('pageDeConnexion.html.twig', [
            'user' => Utilisateur::getUser(),
            'msg_erreur' => $msg_erreur
        ]);
    }

    /**
     * Pous se connecter à la page 
     * d'inscription
     * @return void
     */
    public function pageInscription()
    {
        $msg_erreur = null; // s'il n'y a pas d'erreur, on met la variable à null
        //En cas d'erreur 
        if (isset($_SESSION['msg_erreur'])) {
            $msg_erreur = $_SESSION['msg_erreur'];
            unset($_SESSION['msg_erreur']);
        }

        if (isset($_SESSION['id'])) {
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['id'];
        }

        $template = $this->getTwig();

        echo $template->render('pageInscription.html.twig', [
            'user' => Utilisateur::getUser(),
            'msg_erreur' => $msg_erreur
        ]);
    }

    /**
     * Permet d'inscrire les données de
     *  l'utilisateur dans la base de données
     *  tout en chiffrant le mot de passe
     * 
     * @param Utilisateur $user
     * @throws Exception
     * @return void
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
     * Permet de récupérer les informations
     *  de l'utilisateur depuis le formulaire
     *  et les inscrits dans la BD
     * 
     * @return void
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
                        $_SESSION['msg_erreur'] = "Ce compte existe déjà.";
                        header("Location: index.php?controleur=utilisateur&methode=pageInscription");
                        exit();

                    case "mdp_faible":
                        $_SESSION['msg_erreur'] = "Erreur : Mot de passe invalide.
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
     * Réinitialise les tentatives échouées 
     * après une authentification réussie
     * 
     * @param Utilisateur $user
     * @return void
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
     * Calcul le temps restant avant que le 
     *  compte soit débloqué
     * @param Utilisateur $user
     * @return float|int
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
     * Réactive le compte une fois que le 
     *  délai soit écoulé
     * @param Utilisateur $user
     * @return void
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
     * Gère les échecs de connexion,
     *  incrémente le nombre de tentative échouée
     *  et désactive le compte si le nombre de tentatives 
     *  est supérieur au maximum autorisé (3)
     * @param Utilisateur $user
     * @throws Exception
     * @return never
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
     * Vérifie si les identifiants récupérés
     *  correspondent à ceux de la base 
     *  de données
     * @param Utilisateur $user
     * @throws Exception
     * @return bool
     */
    public function authentification(Utilisateur $user): bool
    {
        // création d'une instance de la bd
        $pdo = $this->getPdo();

        // Recherche de l'utilisateur
        $requete = $pdo->prepare(
            'SELECT id, mdp, tentativesEchouees, dateDernierEchecConnexion, statutCompte, dateSuppression,
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

        // Vérifie si le compte est supprimé
        if ($donneeUtilisateurEnBD['dateSuppression'] !== null) {
            throw new Exception("mail_invalide"); // Or a specific "compte_supprime" message
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
     * Récupère les informations de connexions
     *  de l'utilisateur, vérifie
     *  s'ils sont valides et 
     *  affiche la page d'accueil
     *  selon le role de l'utilisateur
     *  (particulier - étudiant)
     * @return void
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
     *  Affiche les informations du
     *  du compte de l'utilisateur
     * @return void
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
     * Affiche la page de 
     * modification du compte
     * @return void
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
     * Se déconnecte et affiche
     *  la page d'accueil
     * @return never
     */
    public function deconnexion(): void
    {
        $_SESSION = []; // On vide le tableau, pour libérer de l'espace
        session_destroy();
        header('Location: index.php');
        exit();
    }
    /**
     * @brief Traite la modification du compte.
     */
    public function modifierCompte()
    {
        $currentUser = Utilisateur::getUser();
        if (!$currentUser) {
            header('Location: index.php?controleur=utilisateur&methode=pageConnexion');
            exit();
        }

        $template = $this->getTwig();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? $currentUser->getNom();
            $prenom = $_POST['prenom'] ?? $currentUser->getPrenom();
            $dateNaiss = $_POST['dateNaiss'] ?? $currentUser->getDateNaiss();
            $email = $_POST['email'] ?? $currentUser->getEmail();
            $tel = $_POST['tel'] ?? $currentUser->getTel();
            $ville = $_POST['ville'] ?? $currentUser->getVille();
            $adresse = $_POST['adresse'] ?? $currentUser->getAdresse();
            $codePostal = $_POST['codePostal'] ?? $currentUser->getCodePostal();

            // Vérification email si changé
            if ($email != $currentUser->getEmail()) {
                if (Valide::emailExiste($email)) {
                    echo $template->render('pageModifierCompte.html.twig', [
                        'user' => $currentUser,
                        'errEmail' => "Cet email est déjà utilisé.",
                        'err' => ""
                    ]);
                    return;
                }
            }

            // Mise à jour des champs
            $currentUser->setNom($nom);
            $currentUser->setPrenom($prenom);
            $currentUser->setDateNaiss($dateNaiss);
            $currentUser->setEmail($email);
            $currentUser->setTel($tel);
            $currentUser->setVille($ville);
            $currentUser->setAdresse($adresse);
            $currentUser->setCodePostal($codePostal);

            // Gestion spécifique Étudiant
            if ($currentUser instanceof Etudiant) {
                $codeINE = $_POST['codeINE'] ?? $currentUser->getCodeINE();
                $currentUser->setCodeINE($codeINE);
            }

            // Gestion Mot de passe
            $mdp = $_POST['mdp'] ?? '';
            if (!empty($mdp)) {
                if (!Valide::estRobuste($mdp)) {
                    echo $template->render('pageModifierCompte.html.twig', [
                        'user' => $currentUser,
                        'err' => "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial."
                    ]);
                    return;
                }
                $currentUser->setMdp(password_hash($mdp, PASSWORD_BCRYPT));
            } else {
                // Si pas de nouveau mot de passe, on doit récupérer l'ancien hash car le getUser() le vide
                // Astuce : on ne touche pas au mot de passe s'il est vide, mais le DAO l'attend.
                // Le getUser() a fait setMdp("") pour la sécurité.
                // Il faut récupérer le vrai mdp en base si on ne le change pas ?
                // OU mieux : UtilisateurDAO::update doit gérer ça. Mais ma méthode update update tout.
                // Donc je dois récupérer l'ancien mdp haché.
                $bd = $this->getPdo();
                $req = $bd->prepare("SELECT mdp FROM Utilisateur WHERE id = :id");
                $req->execute(['id' => $currentUser->getId()]);
                $oldMdp = $req->fetchColumn();
                $currentUser->setMdp($oldMdp);
            }

            // Sauvegarde dans la BD
            try {
                $dao = new UtilisateurDAO($this->getPdo());
                $dao->update($currentUser);

                // Redirection vers la page compte avec succès
                header("Location: index.php?controleur=utilisateur&methode=afficheCompte");
                exit();

            } catch (Exception $e) {
                echo $template->render('pageModifierCompte.html.twig', [
                    'user' => $currentUser,
                    'err' => "Erreur lors de la mise à jour : " . $e->getMessage()
                ]);
            }
        } else {
            // Si pas POST, redirection ou affichage formulaire (géré par pageModifierCompte)
            $this->pageModifierCompte();
        }
    }

    /**
     * Inscrit l'utilisateur 
     *  à la newsletter
     * @return void
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

    public function gererUtilisateurs()
    {
        $template = $this->getTwig();
        if (!Utilisateur::getUser() || Utilisateur::getUser()->getRole() !== 'Etudiant') {
            header('Location: index.php');
            exit;
        }

        echo $template->render('pageGererUtilisateurs.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    public function gererAnnonces()
    {
        $template = $this->getTwig();
        if (!Utilisateur::getUser() || Utilisateur::getUser()->getRole() !== 'Etudiant') {
            header('Location: index.php');
            exit;
        }

        echo $template->render('pageGererAnnonces.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    public function gererNote()
    {
        $template = $this->getTwig();
        if (!Utilisateur::getUser() || Utilisateur::getUser()->getRole() !== 'Etudiant') {
            header('Location: index.php');
            exit;
        }

        echo $template->render('pageGererNotes.html.twig', [
            'user' => Utilisateur::getUser(),
        ]);
    }

    /**
     * @brief Supprime le compte de l'utilisateur courant.
     */
    public function supprimerCompte()
    {
        $currentUser = Utilisateur::getUser();
        if ($currentUser) {
            $dao = new UtilisateurDAO($this->getPdo());
            $dao->delete($currentUser->getId());
            $this->deconnexion();
        } else {
            header('Location: index.php');
            exit();
        }
    }
}
