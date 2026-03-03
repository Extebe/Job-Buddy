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

        foreach ($annonces as $key => $annonce){
            $tab[$key] = $managerAnnonce->addRelations($annonce);
        }

        $variable=null;
        if(isset($_SESSION["msg"])){
            $variable = $_SESSION["msg"];
            unset($_SESSION["msg"]);
        }

        // $feedbackSignalement=null;
        // if(isset($_SESSION['d'])){
        //     $feedbackSignalement=$_SESSION['d'];
        //     echo $feedbackSignalement;
        //     unset($_SESSION['d']);
        // }
        $_SESSION['lienRetour'] = "index.php?controleur=annonce&methode=afficher";

        echo $template->render('index.html.twig', [
            'annonces' => $tab,
            'icons' => $icons,
            'user' => Utilisateur::getUser(),
            'notif'=> $variable,
            // 'notifSignalement'=> $feedbackSignalement
        ]);
    }

    /**
     * @brief Affiche le formulaire d'ajout d'annonce.
     */
    public function afficheFormAnnonce()
    {
        $msg_erreur = null; // s'il n'y a pas d'erreur, on met la variable à null
        $dataForm=null;
        //En cas d'erreur 
        if (isset($_SESSION['msg_erreur'])) {
            $msg_erreur = $_SESSION['msg_erreur'];
            unset($_SESSION['msg_erreur']);
        }

        if (isset($_SESSION['dataForm'])) {
            $dataForm = $_SESSION['dataForm'];
            unset($_SESSION['dataForm']);
        }

        if (isset($_SESSION['role'])) {
            //À faire, verifier qu'ils sont valides
            $role = $_SESSION['role'];
        } else {
            $role = "non_connecte";
        }

        $template = $this->getTwig();

        echo $template->render('ajouterAnnonce.html.twig', [
            'role' => $role,
            'user' => Utilisateur::getUser(),
            'msg_erreur'=>$msg_erreur,
            'dataForm'=>$dataForm

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

            $formData=[
                'titre'=>$titre,
                'typeService'=>$typeService,
                'dateDebut'=>$dateDebut,
                'dateFin'=>$dateFin,
                'description'=>$description,
                'lieu'=>$lieu,
                'remuneration'=>$remuneration
            ];

            $dateActuelle=new DateTime();
            $dateDebutCompar = new DateTime($dateDebut);
            $dateFinCompar = new  DateTime($dateFin);

            $particulierDao = new ParticulierDao($this->getPdo());
            $particulier = $particulierDao->find(Utilisateur::getUser()->getId());

            try {
                if (!$particulier) {
                    throw new Exception("erreurCompte");
                }

                if($dateDebutCompar < $dateActuelle){
                throw new Exception("datePasse");
                }

                if($dateFinCompar < $dateActuelle || $dateFinCompar < $dateDebutCompar){
                    throw new Exception("dateFinSupDeb");
                }

            } catch (Exception $e) {
                switch($e->getMessage()){
                    case "erreurCompte":
                        header("Location: index.php?controleur=annonce&methode=afficheFormAnnonce");
                        $_SESSION['msg_erreur'] = "Impossible de récupérer le profil Particulier. Vérifiez que vous êtes connecté avec un compte Particulier.";
                        $_SESSION['dataForm']= $formData;
                        exit();
                    case "datePasse":
                        header("Location: index.php?controleur=annonce&methode=afficheFormAnnonce");
                        $_SESSION['msg_erreur'] = "La date de début ne peut pas être dans le passé.";
                        $_SESSION['dataForm']= $formData;
                        exit();
                    case "dateFinSupDeb":
                        header("Location: index.php?controleur=annonce&methode=afficheFormAnnonce");
                        $_SESSION['msg_erreur'] = "La date de fin doit être après la date de début.";
                        $_SESSION['dataForm']= $formData;
                        exit();
                };
            }
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

            $_SESSION["ajoutAnnonce"]="L'annonce a bien été créée.";
            $managerAnnonce = new AnnonceDAO($this->getPdo());
            $managerAnnonce->insererAnnonce($annonce1);
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
        $msg=null;

        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }
        if (!isset($_GET['filtre'])) {
            $filtre = "all";
        } else {
            $filtre = $_GET['filtre'];
        }
        if(isset($_SESSION['suppAnnonce'])){
            $msg = $_SESSION['suppAnnonce'];
            unset($_SESSION['suppAnnonce']);
        }
        if(isset($_SESSION['ajoutAnnonce'])){
            $msg=$_SESSION['ajoutAnnonce'];
            unset($_SESSION['ajoutAnnonce']);
        }
        if(isset($_SESSION['modifAnnonce'])){
            $msg=$_SESSION['modifAnnonce'];
            unset($_SESSION['modifAnnonce']);
        }

        if ($filtre === 'all' || $filtre === '') {
            $tableau = $managerAnnonce->findAllById(Utilisateur::getUser()->getId());
        } else {
            $tableau = $managerAnnonce->findAllByIdAndEtat(Utilisateur::getUser()->getId(), $filtre);
        }
        foreach ($tableau as $key => $annonce) {
            $managerAnnonce->addRelations($annonce);
        }
        $_SESSION['lienRetour'] = "index.php?controleur=annonce&methode=afficherMesAnnonces";

        
        echo $template->render('mesAnnonces.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonces' => $tableau,
            'icons' => Constantes::getConstantes()['icons'],
            'msg' =>$msg
        ]);

    }


    /**
     * @brief Affiche les détails d'une annonce.
     */
    public function afficherDetail()
{
    $template = $this->getTwig();

    // 1. Auth Check
    if (!isset($_SESSION['id'])) {
        header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
        exit();
    }

    // 2. Input Validation
    // Good practice: Ensure ID is actually an integer
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        header("Location: index.php");
        exit();
    }
    $idAnnonce = (int)$_GET['id'];

    $managerAnnonce = new AnnonceDao($this->getPdo());
    $annonce = $managerAnnonce->find($idAnnonce);

    // 3. Safety Check: Does the announcement exist?
    if (!$annonce) {
        // Redirect or show a 404 error if the ID is invalid
        header("Location: index.php"); 
        exit();
    }

    // 4. Hydration Logic
    if ($annonce->getEtuditantsSelectionnes() == null) {
        $annonce = $managerAnnonce->addRelations($annonce);
    } else {
        $annonce = $managerAnnonce->addSelectedStudents($annonce);
    }

    // 5. Initialize $aPostule Default Value (The Fix)
    // We set this to false (or null) by default so the variable exists for ALL roles.
    // On l'initialise à false par défaut pour que la variable existe toujours
    $aPostule = false; 

    // 1. On récupère l'utilisateur. S'il n'est pas connecté, ça vaudra 'null'.
    $utilisateurConnecte = Utilisateur::getUser();

    // 2. On vérifie SI l'utilisateur est bien connecté AVANT de vérifier ses postulations
    if ($utilisateurConnecte !== null) {
        
        // On boucle uniquement si on a bien un tableau valide de postulations
        $postulations = $annonce->getPostulations();
        if (is_iterable($postulations)) {
            foreach ($postulations as $etudiant) {
                
                // Ici, plus de risque de crash : on sait que $utilisateurConnecte existe !
                if ($etudiant->getId() === $utilisateurConnecte->getId()) {
                    $aPostule = true;
                    break;
                }
            }
        }
    }
    $createur = $annonce->getCreateur();
    $managerUtilisateur = new UtilisateurDao($this->getPdo());
    $createur = $managerUtilisateur->findById($createur->getId());

    echo $template->render('detailAnnonce.html.twig', [
        'user'    => Utilisateur::getUser(),
        'annonce' => $annonce,
        'createur' => $createur,
        'icons'   => Constantes::getConstantes()['icons'],
        'aPostule' => $aPostule ,
        'lienRetour' => $_SESSION['lienRetour'] ?? "index.php?controleur=annonce&methode=afficher",
        'etudiants' => $postulations ?? [] // Passer un tableau vide si aucune postulation, pour éviter les erreurs dans Twig
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

        if (Utilisateur::getUser()->getRole() !== 'etudiant') {
            header("Location: index.php");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->postuler($idAnnonce, Utilisateur::getUser()->getId());

        header("Location: index.php");
        exit();
    }

    public function depostulerAnnonce()
    {
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?controleur=utilisateur&methode=pageConnexion");
            exit();
        }

        if (Utilisateur::getUser()->getRole() !== 'etudiant') {
            header("Location: index.php");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $idAnnonce = $_GET['id'];

        $managerAnnonce = new AnnonceDAO($this->getPdo());
        $managerAnnonce->dePostuler($idAnnonce, Utilisateur::getUser()->getId());

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
        
        // Cas de succès (Suppression)
        $_SESSION['suppAnnonce'] = 'L\'annonce a bien été supprimer.';

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
            $annonce->setEtat('confirme');
            header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
            exit();
        }
        if ($_GET['action'] === 'refuser') {
            $managerAnnonce->refuserEtudiant($idAnnonce, $idEtudiant);
            header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
            exit();
        }
    }

    public function confirmePostulation(){

        $idEtudiant = $_GET['idEtudiant'];
        $idAnnonce = $_GET['idAnnonce'];
        $managerAnnonce = new AnnonceDao($this->getPdo());
        $annonce = $managerAnnonce->find($idAnnonce);

        if ($idEtudiant != Utilisateur::getUser()->getId()) {
            throw new Exception("Vous n'êtes pas autorisé à accepter cette mission.");
        }

        if ($_GET['action'] === 'confirme'){
            $managerAnnonce->confirmerAnnonce($idEtudiant, $idAnnonce);
            $annonce->setEtat('confirme');
            header("Location: index.php?controleur=annonce&methode=afficherDetail&id=" . $idAnnonce);
            exit();
        }

        if ($_GET['action'] === 'refuser'){
            $managerAnnonce->refuserEtudiant($idAnnonce, $idEtudiant);
            header("Location: index.php");
            exit();
        }
    }

    /**
     * @brief Edite une annonce existante.
     */
    public function editerAnnonce(): void
    {   
        //si la méthode est appelé depuis un formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCreateur = $_POST['idCreateur'];

            $managerParticulier = new ParticulierDAO($this->getPdo());
            $particulier = $managerParticulier->find($idCreateur);
            
            $annonce = new Annonce(
                $_POST['idAnnonce'],
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
            $_SESSION['modifAnnonce']="L'annonce a bien été modifié.";
            header('Location:index.php?controleur=annonce&methode=afficherMesAnnonces');
            exit();
        }

        else {
            // récupération des id de l'annonce et de son créateur depuis detailAnnonce.html.twig
            $idAnnonce = $_GET['id'];
            $user = Utilisateur::getUser();
            $idCreateur = $user->getId();
            $annonceDao = new AnnonceDao($this->getPdo());
            $annonce = $annonceDao->findById($idCreateur,$idAnnonce);

            $template = $this->getTwig();
        }
        echo $template->render('modifierAnnonce.html.twig', [
            'user' => $user,
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
        $ordre = $_POST['sortBy'] ?? 'date'; // Par défaut, trier par date
        
        // Construire la requête de recherche
        $annonces = [];
        if (!empty($recherche) || !empty($typeService) || !empty($ordre)) {
            // Utiliser la méthode search pour filtrer
            $annonces = $managerAnnonce->search($recherche, $typeService, $ordre);
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
            'sortBy' => $ordre
        ]);
    }


    public function afficherPolitiqueConfidentialite()
    {
        $template = $this->getTwig();
        echo $template->render('politiqueConfidentialite.html.twig', ['user' => Utilisateur::getUser()]);
    }

    /**
     * Inscrit l'utilisateur 
     *  à la newsletter
     * @return void
     */
    public function newsletter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['emailNewsletter'];
            $pdoNewsLetter = new NewLetterDao($this->getPdo());
            $managerAnnonce = new AnnonceDao($this->getPdo());
            $tableau = $managerAnnonce->findAllAssocDispo();
            $annonces = $managerAnnonce->hydrateAll($tableau);
            $icons = Constantes::getConstantes()['icons'];

            
            foreach ($annonces as $key => $annonce) {
                $tab[$key] = $managerAnnonce->addRelations($annonce);
            }

            if (!$pdoNewsLetter->emailExisteNewsletter($email)) {
                $newsLetter = new NewLetter(null, $email);
                $pdoNewsLetter->insererEmail($newsLetter);
                $_SESSION['msg'] = "newletterSuccess";
            }
            else{
                $_SESSION['msg'] = "newletterFailed";
            }
            header("Location:index.php?controleur=annonce&methode=afficher");

        }
    }
    

    public function afficherMaps()
    {
        $template = $this->getTwig();

        $managerAnnonce = new AnnonceDao($this->getPdo());
        $tableau = $managerAnnonce->findAllAssocDispo();
        $annonces = $managerAnnonce->hydrateAll($tableau);

        foreach ($annonces as $key => $annonce){
            $tab[$key] = $managerAnnonce->addRelations($annonce);
        }

        echo $template->render('maps.html.twig', [
            'user' => Utilisateur::getUser(),
            'annonces' => $tab
        ]);
    }
}