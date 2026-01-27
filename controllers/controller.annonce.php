<?php

/**
 * @brief Contrôleur gérant les annonces.
 */
class ControllerAnnonce extends Controller
{
    /**
     * @brief Constructeur du contrôleur Annonce.
     * @param \Twig\Environment $twig Environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
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
        $tableau = $managerAnnonce->findAllAssocDispo();
        $annonces = $managerAnnonce->hydrateAll($tableau);
        $icons = Constantes::getConstantes()['icons'];

        echo $template->render('index.html.twig', [
            'annonces' => $annonces,
            'icons' => $icons,
            'user' => Utilisateur::getUser()
        ]);
    }

    /**
     * @brief Affiche le formulaire d'ajout d'annonce.
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
     * @brief Traite le formulaire d'ajout d'annonce.
     */
    public function traiteFormAnnonce()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titreAnnonce'];
            $typeService = mb_strtolower($_POST['typeService'], 'UTF-8');
            $dateDebut = $_POST['dateDebut'];
            $dateFin = $_POST['dateFin'];
            $description = $_POST['description'];
            $lieu = $_POST['lieu'];
            $remuneration = $_POST['prix'];

            $particulierDao = new ParticulierDao($this->getPdo());
            $particulier = $particulierDao->find(Utilisateur::getUser()->getId());

            $annonce1 = new Annonce(
                null,
                $particulier,
                $titre,
                $description,
                $typeService,
                $lieu,
                (float) $remuneration,
                $dateDebut,
                $dateFin,
                "disponible",
                date("Y-m-d H:i:s"),
                null,
                null
            );
            $managerAnnonce = new AnnonceDAO($this->getPdo());
            $managerAnnonce->insererAnnonce($annonce1);
            try {
                if (!$particulier) {
                    throw new Exception("Erreur : Impossible de récupérer le profil Particulier. Verifiez que vous tes connecté avec un compte Particulier.");
                }
            } catch (\Exception $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
            //Appel de la requête qui créé l'annonce
            header("Location: index.php?controleur=annonce&methode=afficherMesAnnonces");
            exit();

        }
    }


    /**
     * @brief Liste les annonces (non implémenté).
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
            $filtre = "all";
        } else {
            $filtre = $_GET['filtre'];
        }

        if ($filtre === 'all' || $filtre === '') {
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
     * @brief Permet à un particulier de supprimer son annonce.
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

        header("Location: index.php?controleur=annonce&methode=afficherMesAnnonces");
        exit();
    }

    /**
     * @brief Refuse la candidature d'un étudiant (non implémenté ici, voir selectionnerEtudiant).
     * @param int $idAnnonce ID de l'annonce.
     * @param int $idEtudiant ID de l'étudiant.
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

    /**
     * @brief Edite une annonce existante.
     */
    public function editerAnnonce(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idAnnonce = $_POST['idAnnonce'];
            $idCreateur = $_POST['idCreateur'];

            $managerParticulier = new ParticulierDAO($this->getPdo());
            $particulier = $managerParticulier->find($idCreateur);
            
            $annonce = new Annonce(
                $idAnnonce,
                $particulier,
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
            $annonceDao= New AnnonceDao($this->getPdo());
            $annonceDao->modifier($annonce);
            header('Location:index.php?controleur=annonce&methode=afficherMesAnnonces');
            exit();
        }

        else {
            // récupération des id de l'annonce et de son créateur depuis detailAnnonce.html.twig
            $idAnnonce = $_GET['idAnnonce'];
            $idCreateur = $_GET['idCreateur'];

            $annonceDao = new AnnonceDao($this->getPdo());
            $annonce = $annonceDao->findById($idCreateur,$idAnnonce);

            $template = $this->getTwig();
        }
        echo $template->render('modifierAnnonce.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonce' => $annonce,
        ]);
    }

    /**
     * @brief Affiche les statistiques des annonces par type de service.
     * 
     * Utilise une requête SQL avec sous-requête et GROUP BY pour calculer :
     * - Le nombre d'annonces par type de service
     * - La rémunération moyenne et totale
     * - Le nombre total de postulants par type
     */
    public function afficherStatistiques(): void
    {
        $template = $this->getTwig();

        $managerAnnonce = new AnnonceDao($this->getPdo());
        $statistiques = $managerAnnonce->getStatistiquesParType();

        // Calcul des totaux globaux
        $totaux = [
            'nbAnnonces' => 0,
            'remunerationTotale' => 0,
            'totalPostulants' => 0
        ];

        foreach ($statistiques as $stat) {
            $totaux['nbAnnonces'] += $stat['nbAnnonces'];
            $totaux['remunerationTotale'] += $stat['remunerationTotale'];
            $totaux['totalPostulants'] += $stat['totalPostulants'];
        }

        echo $template->render('statistiques.html.twig', [
            'user' => Utilisateur::getUser(),
            'statistiques' => $statistiques,
            'totaux' => $totaux,
            'icons' => Constantes::getConstantes()['icons']
        ]);
    }

    /**
     * @brief Recherche et filtre des annonces.
     */
    public function rechercher() {
        $template = $this->getTwig();
        $managerAnnonce = new AnnonceDao($this->getPdo());
        
        // Récupérer les paramètres de recherche et filtres
        $recherche = $_POST['recherche'] ?? '';
        $typeService = $_POST['typeService'] ?? '';
        $lieu = $_POST['lieu'] ?? '';
        $remunerationMin = $_POST['remunerationMin'] ?? '';
        $remunerationMax = $_POST['remunerationMax'] ?? '';
        $dateDebut = $_POST['dateDebut'] ?? '';
        $heureDebut = $_POST['heureDebut'] ?? '';
        
        // Construire la requête de recherche
        $annonces = [];
        if (!empty($recherche) || !empty($typeService) || !empty($lieu) || !empty($remunerationMin) || !empty($remunerationMax) || !empty($dateDebut) || !empty($heureDebut)) {
            // Utiliser la méthode search pour filtrer
            $annonces = $managerAnnonce->search($recherche, $typeService, $lieu, $remunerationMin, $remunerationMax, $dateDebut, $heureDebut);
        } else {
            // Si aucun critère, afficher toutes les annonces disponibles
            $tableau = $managerAnnonce->findAllAssocDispo();
            $annonces = $managerAnnonce->hydrateAll($tableau);
        }

        echo $template->render('index.html.twig', [
            'annonces' => $annonces,
            'icons' => Constantes::getConstantes()['icons'],
            'user' => Utilisateur::getUser(),
            'recherche' => $recherche,
            'typeService' => $typeService,
            'lieu' => $lieu,
            'remunerationMin' => $remunerationMin,
            'remunerationMax' => $remunerationMax,
            'dateDebut' => $dateDebut,
            'heureDebut' => $heureDebut
        ]);
    }
}