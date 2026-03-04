<?php
require_once "include.php";

/**
 * @file    controller.utilisateur.php
 * @brief
 */

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * Pour se connecter à la page 
     * de connexion
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
     * Pour se connecter à la page 
     * d'inscription
     * @return void
     */
    public function pageInscription()
    {
        $template = $this->getTwig();

        echo $template->render('pageInscription.html.twig', [
            'user' => Utilisateur::getUser(),
            'msg_erreur' => null,
            'formData' => []
        ]);
    }

    /**
     * Permet d'inscrire les données de
     * l'utilisateur dans la base de données
     * tout en chiffrant le mot de passe
     * * @param Utilisateur $user
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
        if ($user instanceof Etudiant && Valide::ineExiste($user->getCodeINE())) {
            throw new Exception("INE _utilise");
        }

        if ($user instanceof Etudiant && Valide::cvecExiste($user->getCvec())) {
            throw new Exception("CVEC_utilise");
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
     * de l'utilisateur depuis le formulaire
     * et les inscrits dans la BD
     * * @return void
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

            // Données du formulaire à transmettre à la vue en cas d'erreur
            $formData = [
                'nom' => $nom,
                'prenom' => $prenom,
                'datenaiss' => $dateNaiss,
                'phone' => $phone,
                'role' => $role,
                'codeINE' => $codeINE,
                'ville' => $ville,
                'adresse' => $adresse,
                'codePostal' => $codePostal,
                'email' => $email,
                'cvec' => $cvec
            ];

            if ($role == 'etudiant') {
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
                $template = $this->getTwig();
                $msg_erreur = "";

                switch ($e->getMessage()) {
                    case "compte_existant":
                        $msg_erreur = "Ce compte existe déjà.";
                        break;
                    case "mdp_faible":
                        $msg_erreur = "Erreur : Mot de passe invalide. Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
                        break;
                    case "CVEC invalide":
                        $msg_erreur = "Erreur : CVEC invalide.";
                        break;
                    case "INE _utilise":
                        $msg_erreur = "Erreur : Ce code INE est déjà utilisé.";
                        break;
                    case "CVEC_utilise":
                        $msg_erreur = "Erreur : Ce CVEC est déjà utilisé.";
                        break;
                    default:
                        $msg_erreur = "Une erreur inattendue est survenue : {$e->getMessage()}";
                }

                // Affichage du formulaire avec les données et le message d'erreur
                echo $template->render('pageInscription.html.twig', [
                    'user' => Utilisateur::getUser(),
                    'msg_erreur' => $msg_erreur,
                    'formData' => $formData
                ]);
            }
        }
    }

    /**
     * Réinitialise les tentatives échouées 
     * après une authentification réussie
     * * @param Utilisateur $user
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
     * compte soit débloqué
     * @param Utilisateur $user
     * @return float|int
     */
    public function tempsRestantAvantDeblocage(Utilisateur $user): int
    {
        $constantesConnexion = Constantes::getConstantes()['tentative'];
        if (!$user->getDateDernierEchecConnexion()) {
            return 0;
        }
        $dernierEchecTimestamp = strtotime($user->getDateDernierEchecConnexion());
        $tempsEcoule = time() - $dernierEchecTimestamp;
        $tempsRestant = $constantesConnexion['DELAI_ATTENTE_CONNEXION'] - $tempsEcoule;
        return $tempsRestant > 0 ? $tempsRestant : 0;
    }

    /**
     * Réactive le compte une fois que le 
     * délai soit écoulé
     * @param Utilisateur $user
     * @return void
     */
    public function reactiverCompte(Utilisateur $user): void
    {
        $user->setTentativesEchouees(0);
        $user->setDateDernierEchecConnexion(null);
        $user->setStatutCompte('actif');

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
     * incrémente le nombre de tentative échouée
     * et désactive le compte si le nombre de tentatives 
     * est supérieur au maximum autorisé (3)
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
     * correspondent à ceux de la base 
     * de données
     * @param Utilisateur $user
     * @throws Exception
     * @return bool
     */
    public function authentification(Utilisateur $user): bool
    {
        $pdo = $this->getPdo();

        $requete = $pdo->prepare(
            'SELECT id, mdp, tentativesEchouees, dateDernierEchecConnexion, statutCompte, dateSuppression,
             role FROM Utilisateur WHERE email =:email;'
        );

        $requete->execute(['email' => $user->getEmail()]);
        $donneeUtilisateurEnBD = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$donneeUtilisateurEnBD) {
            throw new Exception("mail_non_existant");
        }

        if ($donneeUtilisateurEnBD['dateSuppression'] !== null) {
            throw new Exception("mail_invalide");
        }

        $user->setId($donneeUtilisateurEnBD['id']);
        $user->setTentativesEchouees($donneeUtilisateurEnBD['tentativesEchouees']);
        $user->setDateDernierEchecConnexion($donneeUtilisateurEnBD['dateDernierEchecConnexion']);
        $user->setStatutCompte($donneeUtilisateurEnBD['statutCompte']);

        if ($user->getStatutCompte() === 'desactive') {
            if ($this->tempsRestantAvantDeblocage($user) !== 0) {
                throw new Exception("compte_desactive");
            }
            $this->reactiverCompte($user);
        }

        if (password_verify($user->getMdp(), $donneeUtilisateurEnBD['mdp'])) {
            if ($user->getTentativesEchouees() > 0) {
                $this->reinitialiserTentativesConnexion($user);
            }
            $_SESSION['id'] = $user->getId();
            return true;
        }
        $this->gererEchecConnexion($user);
        return false;
    }

    /**
     * Récupère les informations de connexions
     * de l'utilisateur, vérifie
     * s'ils sont valides et 
     * affiche la page d'accueil
     * @return void
     */
    public function connexion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['mdp'] ?? '';

            $utilisateur = new Utilisateur(null, null, null, null, null, null, $email, $mdp);

            try {
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
                    case "mail_non_existant":
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
     * Affiche les informations du
     * du compte de l'utilisateur
     * @return void
     */
    public function afficheCompte()
    {
        if (!Utilisateur::getUser()) {
            header('Location: index.php');
            exit;
        }
        $template = $this->getTwig();

        $modifCompte=null;
        if(isset($_SESSION['msg'])){
            $modifCompte=$_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        $managerNote = new NoteDAO($this->getPdo());
        $user = Utilisateur::getUser();
        $user = $managerNote->addNoteRecue($user);
        $moyenne = $user->calculerMoyenneNotes();

        echo $template->render('pageCompte.html.twig', [
            'user' => $user,
            'notif'=>$modifCompte,
            'moyenne'=>$moyenne
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
     * la page d'accueil
     * @return never
     */
    public function deconnexion(): void
    {
        $_SESSION = []; 
        session_destroy();
        header('Location: index.php');
        exit();
    }

    /**
     * @brief Traite la modification du compte (Infos, MDP et Photo avec Écrasement).
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

            // 1. Vérification email si changé
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

            // Mise à jour des champs basiques
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

            // 2. Gestion Mot de passe
            $mdp = $_POST['mdp'] ?? '';
            if (!empty($mdp)) {
                if (!Valide::estRobuste($mdp)) {
                    echo $template->render('pageModifierCompte.html.twig', [
                        'user' => $currentUser,
                        'err' => "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère."
                    ]);
                    return;
                }
                $currentUser->setMdp(password_hash($mdp, PASSWORD_BCRYPT));
            } else {
                $bd = $this->getPdo();
                $req = $bd->prepare("SELECT mdp FROM Utilisateur WHERE id = :id");
                $req->execute(['id' => $currentUser->getId()]);
                $oldMdp = $req->fetchColumn();
                $currentUser->setMdp($oldMdp);
            }

            $dao = new UtilisateurDAO($this->getPdo());

            // 3. GESTION DE LA PHOTO DE PROFIL (AVEC ÉCRASEMENT)
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $fichier = $_FILES['photo'];
                $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
                $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
                $tailleMax = 2 * 1024 * 1024; // 2 Mo max

                if (!in_array($extension, $extensionsAutorisees)) {
                    echo $template->render('pageModifierCompte.html.twig', ['user' => $currentUser, 'err' => "Format d'image non autorisé."]); 
                    return;
                } elseif ($fichier['size'] > $tailleMax) {
                    echo $template->render('pageModifierCompte.html.twig', ['user' => $currentUser, 'err' => "L'image dépasse 2 Mo."]); 
                    return;
                } else {
                    $dossierDestination = './uploads/profiles/';

                    if (!is_dir($dossierDestination)) {
                        mkdir($dossierDestination, 0777, true);
                    }

                    // --- LOGIQUE D'ÉCRASEMENT DE L'ANCIENNE PHOTO ---
                    $anciennePhoto = $currentUser->getPhotoProfil(); 

                    if (!empty($anciennePhoto)) {
                        // On extrait le nom de base pour conserver le même ID dans le nom du fichier
                        $nomDeBase = pathinfo($anciennePhoto, PATHINFO_FILENAME);
                        $nouveauNom = $nomDeBase . '.' . $extension;
                        
                        // Si le format (extension) a changé (ex: .png vers .jpg), on supprime l'ancien fichier
                        if ($anciennePhoto !== $nouveauNom && file_exists($dossierDestination . $anciennePhoto) && is_file($dossierDestination . $anciennePhoto)) {
                            unlink($dossierDestination . $anciennePhoto);
                        }
                    } else {
                        // C'est sa toute première photo, on génère un nom standardisé basé sur son ID
                        $nouveauNom = 'profil_user_' . $currentUser->getId() . '.' . $extension;
                    }

                    $cheminFinal = $dossierDestination . $nouveauNom;
                    $fichierSource = $fichier['tmp_name'];

                    // --- DÉBUT DE LA CRÉATION DE LA VIGNETTE ---
                    list($width, $height, $type) = getimagesize($fichierSource);
                    $max_size = 400;

                    $ratio = $width / $height;
                    if ($width > $height) {
                        $new_width = $max_size;
                        $new_height = $max_size / $ratio;
                    } else {
                        $new_height = $max_size;
                        $new_width = $max_size * $ratio;
                    }

                    $image_p = imagecreatetruecolor($new_width, $new_height);

                    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
                        imagealphablending($image_p, false);
                        imagesavealpha($image_p, true);
                        $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
                        imagefilledrectangle($image_p, 0, 0, $new_width, $new_height, $transparent);
                    }

                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            $image = imagecreatefromjpeg($fichierSource);
                            break;
                        case IMAGETYPE_PNG:
                            $image = imagecreatefrompng($fichierSource);
                            break;
                        case IMAGETYPE_WEBP:
                            $image = imagecreatefromwebp($fichierSource);
                            break;
                        default:
                            $image = null;
                    }

                    if ($image !== null) {
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        
                        // Ces fonctions écrasent physiquement le fichier s'il porte déjà le nom $cheminFinal
                        switch ($type) {
                            case IMAGETYPE_JPEG:
                                imagejpeg($image_p, $cheminFinal, 85); 
                                break;
                            case IMAGETYPE_PNG:
                                imagepng($image_p, $cheminFinal);
                                break;
                            case IMAGETYPE_WEBP:
                                imagewebp($image_p, $cheminFinal, 85);
                                break;
                        }

                        imagedestroy($image_p);
                        imagedestroy($image);
                        
                        $currentUser->setPhotoProfil($nouveauNom); 
                    } else {
                        // Sécurité de repli
                        move_uploaded_file($fichierSource, $cheminFinal);
                        $currentUser->setPhotoProfil($nouveauNom); 
                    }
                }
            }

            // 4. Sauvegarde Globale dans la BD
            try {
                $dao->update($currentUser);
                $_SESSION['msg']="modifCompte";
                header("Location: index.php?controleur=utilisateur&methode=afficheCompte");
                exit();
            } catch (Exception $e) {
                echo $template->render('pageModifierCompte.html.twig', [
                    'user' => $currentUser,
                    'err' => "Erreur lors de la mise à jour : " . $e->getMessage()
                ]);
            }
        } else {
            $this->pageModifierCompte();
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

    /**
     * Pour afficher le compte
     * d'un utilisateur
     * @return void
     */
    public function pageUtilisateur()
    {
        $pdo = $this->getPdo();

        $requete = $pdo->prepare(
            'SELECT id, nom, prenom, tel, ville, codePostal, role, photoProfil FROM Utilisateur WHERE id =:id;'
        );

        $idUser = (int)$_GET['id'];

        $requete->execute(['id' => $idUser]);
        $other = $requete->fetch(PDO::FETCH_ASSOC);
        $managerUtilisateur = new UtilisateurDAO($pdo);
        $other = $managerUtilisateur->findById($idUser);
        $managerNote = new NoteDAO($pdo);
        $template = $this->getTwig();
        $other = $managerNote->addNoteRecue($other);
        $moyenne = $other->calculerMoyenneNotes();
        echo $template->render('compteUtilisateur.html.twig', [
            'user' => Utilisateur::getUser(),
            'other' => $other,
            'moyenne' => $moyenne
        ]);
    }
}