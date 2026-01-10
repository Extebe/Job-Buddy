<?php

/**
 * @brief Contrôleur gérant les annonces (affichage, création, candidature).
 */
class ControllerAnnonce extends Controller
{
    /**
     * @brief Constructeur du contrôleur annonce.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la liste des annonces (page d'accueil).
     */
    public function afficher()
    {
        $template = $this->getTwig();

        //recupération des annonces
        $managerAnnonce = new AnnonceDao($this->getPdo());
        $tableau = $managerAnnonce->findAllAssoc();
        $annonces = $managerAnnonce->hydrateAll($tableau);
        $icons = Constantes::getConstantes()['icons'];

        echo $template->render('index.html.twig', [
            'annonces' => $annonces,
            'icons' => $icons,
            'user' => Utilisateur::getUser()
        ]);


    }

    /**
     * @brief Affiche le formulaire de création d'annonce.
     */
    public function afficheFormAnnonce()
    {

        if (isset($_SESSION['role'])) {
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        } else {
            $role = "non_connecte";
        }

        $template = $this->getTwig();

        echo $template->render('ajouterAnnonce.html.twig', [
            'role' => $role,
            'user' => Utilisateur::getUser()

        ]);
    }

    /**
     * @brief Traite le formulaire de création d'annonce.
     */
    public function traiteFormAnnonce()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $typeService = $_POST['typeService'];
            $dateDebut = $_POST['dateDebut'];
            $dateFin = $_POST['dateFin'];
            $description = $_POST['description'];
            $lieu = $_POST['lieu'];
            $remuneration = $_POST['remuneration'];
            $idParticulier = $_GET['idParticulier'];
            if ($_GET['idParticulier'] != Utilisateur::getUser()->getId()) {
                //Tentative de soumission d'annonce pour un autre utilisateur
                header("Location: index.php");
                exit();
            }
            $particulier = Utilisateur::getUser();
            $annonce1 = new Annonce(
                null,
                $particulier->getId(),
                $titre,
                $description,
                $typeService,
                $lieu,
                $remuneration,
                $dateDebut,
                $dateFin,
                "DISPONIBLE",
                date("Y-m-d H:i:s"),
                null,
                null
            );
            $managerAnnonce = new AnnonceDAO($this->getPdo());
            $managerAnnonce->insererAnnonce($annonce1);
            //Appel de la requête qui créé l'annonce
            header("Location: index.php?controleur=annonce&methode=afficherMesAnnonces&filtre=ALL");
            exit();

        }
    }


    /**
     * @brief Liste les annonces (vide pour l'instant).
     */
    public function listerAnnonces()
    {

    }


    /**
     * @brief Affiche les annonces de l'utilisateur connecté.
     */
    public function afficherMesAnnonces()
    {
        $template = $this->getTwig();


        $managerAnnonce = new AnnonceDao($this->getPdo());
        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }
        if (!isset($_GET['filtre'])) {
            $filtre = "ALL";
        } else {
            $filtre = $_GET['filtre'];
        }

        if ($filtre === 'ALL' || $filtre === '') {
            $tableau = $managerAnnonce->findAllById(Utilisateur::getUser()->getId());
        } else {
            $tableau = $managerAnnonce->findAllByIdAndEtat(Utilisateur::getUser()->getId(), $filtre);
        }
        echo $template->render('mesAnnonces.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonces' => $tableau,
            'icons' => Constantes::getConstantes()['icons']
        ]);

    }


    /**
     * @brief Affiche les détails d'une annonce.
     */
    public function afficherDetail()
    {
        $template = $this->getTwig();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }


        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }
        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDao($this->getPdo());
        $annonce = $managerAnnonce->find($idAnnonce);
        if ($annonce->getEtuditantsSelectionnes() == null) {
            $annonce = $managerAnnonce->addRelations($annonce);
        } else {
            $annonce = $managerAnnonce->addSelectedStudents($annonce);
        }


        echo $template->render('detailAnnonce.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonce' => $annonce,
            'icons' => Constantes::getConstantes()['icons']
        ]);
    }

    /**
     * @brief Permet à un étudiant de postuler à une annonce.
     */
    public function postulerAnnonce()
    {
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->postuler($idAnnonce, Utilisateur::getUser()->getId());

        header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
        exit();
    }

    /**
     * @brief Supprime une annonce.
     */
    public function supprimerAnnonce()
    {
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->supprimer($idAnnonce, Utilisateur::getUser()->getId());

        header("Location: index.php?controleur=annonce&methode=afficherMesAnnonces&filtre=ALL");
        exit();
    }

    /**
     * @brief Refuse un étudiant (vide pour l'instant).
     * @param mixed $idAnnonce ID de l'annonce.
     * @param mixed $idEtudiant ID de l'étudiant.
     */
    public function refuser($idAnnonce, $idEtudiant)
    {




    }

    /**
     * @brief Sélectionne ou refuse un étudiant pour une annonce.
     * @throws Exception Si l'utilisateur n'est pas autorisé.
     */
    public function selectionnerEtudiant()
    {
        $idAnnonce = $_GET['idAnnonce'];
        $idEtudiant = $_GET['idEtudiant'];
        $managerAnnonce = new AnnonceDao($this->getPdo());
        $annonce = $managerAnnonce->find($idAnnonce);

        if ($annonce->getCreateur()->getId() != Utilisateur::getUser()->getId()) {
            throw new Exception("Vous n'êtes pas autorisé à refuser cette candidature.");
        }
        if ($_GET['action'] === 'accepter') {
            // Accepter un étudiant
            $managerAnnonce->accepterEtudiant($idAnnonce, $idEtudiant);
            header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
            exit();
        }
        if ($_GET['action'] === 'refuser') {
            $managerAnnonce->refuserEtudiant($idAnnonce, $idEtudiant);
            header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
            exit();
        }
    }

    /* ===================================
     *
     * Appelle la fonction modifier 
     * de la classe annonceDao pour changer
     * la base de donné en récupérant les 
     * information du formulaire de la 
     * page modifierAnnonce
     * 
      ===================================*/
    /**
     * @brief Édite une annonce existante.
     */
    public function editerAnnonce(): void
    {
        $annonce = Annonce::getAnnonce();
        $annonceDao = new AnnonceDao($this->getPdo());
        $template = $this->getTwig();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $idAnnonce = $_POST['idAnnonce'];
            $idParticulier = $annonce->getCreateur()->getId();

            $annonce = new Annonce(
                $idAnnonce,
                $idParticulier,
                $_POST['titre'],
                $_POST['description'],
                $_POST['typeService'],
                $_POST['lieu'],
                $_POST['remuneration'],
                $_POST['dateDebut'],
                $_POST['dateFin'],
                null,
                null,
                null,
                null
            );
            $annonceDao->modifier($annonce);
            header('Location:index.php?controleur=annonce&methode=afficherMesAnnonces');
            exit();
        }
        // var_dump($annonce);
        // echo $annonce->getCreateur()->getId();
        echo $template->render('modifierAnnonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}