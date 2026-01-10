<?php

require_once "include.php";

/**
 * @brief Classe représentant un utilisateur générique.
 * @details Cette classe sert de parent pour les étudiants et les particuliers.
 */
class Utilisateur
{
    /** @var int|null $id Identifiant de l'utilisateur. */
    protected ?int $id;
    /** @var string|null $nom Nom de l'utilisateur. */
    protected ?string $nom;
    /** @var string|null $prenom Prénom de l'utilisateur. */
    protected ?string $prenom;
    /** @var string|null $tel Numéro de téléphone. */
    protected ?string $tel;
    /** @var string|null $dateNaiss Date de naissance (format YYYY-MM-DD). */
    protected ?string $dateNaiss;
    /** @var string|null $role Rôle de l'utilisateur (ex: ETUDIANT, PARTICULIER). */
    protected ?string $role;
    /** @var string|null $email Adresse email. */
    protected ?string $email;
    /** @var string|null $mdp Mot de passe (haché). */
    protected ?string $mdp;
    /** @var string|null $adresse Adresse postale. */
    protected ?string $adresse;
    /** @var string|null $ville Ville de résidence. */
    protected ?string $ville;
    /** @var string|null $codePostal Code postal. */
    protected ?string $codePostal;
    /** @var string|null $dateSuppression Date de suppression du compte. */
    protected ?string $dateSuppression;
    /** @var int $tentativesEchouees Nombre de tentatives de connexion échouées. */
    protected int $tentativesEchouees;
    /** @var string|null $dateDernierEchecConnexion Date du dernier échec de connexion. */
    protected ?string $dateDernierEchecConnexion;
    /** @var string|null $statutCompte Statut du compte (ex: actif, desactive). */
    protected ?string $statutCompte;
    /** @var array $notesDonnees Liste des notes données par l'utilisateur. */
    protected array $notesDonnees = [];
    /** @var array $notesRecues Liste des notes reçues par l'utilisateur. */
    protected array $notesRecues = [];

    /**
     * @brief Constructeur de la classe Utilisateur.
     * 
     * @param int|null $id Identifiant unique.
     * @param string|null $nom Nom de famille.
     * @param string|null $prenom Prénom.
     * @param string|null $tel Numéro de téléphone.
     * @param string|null $dateNaiss Date de naissance.
     * @param string|null $role Rôle de l'utilisateur.
     * @param string|null $email Adresse email.
     * @param string|null $mdp Mot de passe.
     * @param string|null $adresse Adresse postale.
     * @param string|null $ville Ville.
     * @param string|null $codePostal Code postal.
     * @param string|null $dateSuppression Date de suppression.
     * @param int $tentativesEchouees Nombre de tentatives échouées (défaut: 0).
     * @param string|null $dateDernierEchecConnexion Date dernier échec.
     * @param string $statutCompte Statut du compte (défaut: 'actif').
     */
    public function __construct(?int $id = null, ?string $nom = null, ?string $prenom = null, ?string $tel = null, ?string $dateNaiss = null, ?string $role = null, ?string $email = null, ?string $mdp = null, ?string $adresse = null, ?string $ville = null, ?string $codePostal = null, ?string $dateSuppression = null, ?int $tentativesEchouees = 0, ?string $dateDernierEchecConnexion = null, ?string $statutCompte = 'actif')
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->tel = $tel;
        $this->dateNaiss = $dateNaiss;
        $this->role = $role;
        $this->email = $email;
        $this->mdp = $mdp;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
        $this->dateSuppression = $dateSuppression;
        $this->tentativesEchouees = $tentativesEchouees;
        $this->dateDernierEchecConnexion = $dateDernierEchecConnexion;
        $this->statutCompte = $statutCompte;
    }

    /**
     * @brief Récupère l'identifiant.
     * @return int|null L'identifiant.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @brief Définit l'identifiant.
     * @param int|null $id L'identifiant.
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @brief Récupère le nom.
     * @return string|null Le nom.
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @brief Définit le nom.
     * @param string|null $nom Le nom.
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @brief Récupère le prénom.
     * @return string|null Le prénom.
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @brief Définit le prénom.
     * @param string|null $prenom Le prénom.
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @brief Récupère le téléphone.
     * @return string|null Le téléphone.
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @brief Définit le téléphone.
     * @param string|null $telephone Le téléphone.
     */
    public function setTel($telephone)
    {
        $this->tel = $telephone;
    }

    /**
     * @brief Récupère la date de naissance.
     * @return string|null La date de naissance.
     */
    public function getDateNaiss()
    {
        return $this->dateNaiss;
    }

    /**
     * @brief Définit la date de naissance.
     * @param string|null $dateNaiss La date de naissance.
     */
    public function setDateNaiss($dateNaiss)
    {
        $this->dateNaiss = $dateNaiss;
    }

    /**
     * @brief Récupère le rôle.
     * @return string|null Le rôle.
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @brief Définit le rôle.
     * @param string|null $role Le rôle.
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @brief Récupère l'email.
     * @return string|null L'email.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @brief Définit l'email.
     * @param string|null $email L'email.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @brief Récupère le mot de passe.
     * @return string|null Le mot de passe.
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @brief Définit le mot de passe.
     * @param string|null $mdp Le mot de passe.
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * @brief Récupère l'adresse.
     * @return string|null L'adresse.
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @brief Définit l'adresse.
     * @param string|null $adresse L'adresse.
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @brief Récupère la ville.
     * @return string|null La ville.
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @brief Définit la ville.
     * @param string|null $ville La ville.
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * @brief Récupère le code postal.
     * @return string|null Le code postal.
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @brief Définit le code postal.
     * @param string|null $codePostal Le code postal.
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @brief Récupère la date de suppression.
     * @return string|null La date de suppression.
     */
    public function getDateSuppression()
    {
        return $this->dateSuppression;
    }

    /**
     * @brief Définit la date de suppression.
     * @param string|null $dateSuppression La date de suppression.
     */
    public function setDateSuppression($dateSuppression)
    {
        $this->dateSuppression = $dateSuppression;
    }

    /**
     * @brief Récupère les notes données par l'utilisateur.
     * @return array La liste des notes données.
     */
    public function getNotesDonnees(): array
    {
        return $this->notesDonnees;
    }

    /**
     * @brief Récupère les notes reçues par l'utilisateur.
     * @return array La liste des notes reçues.
     */
    public function getNotesRecues(): array
    {
        return $this->notesRecues;
    }

    /**
     * @brief Définit les notes données.
     * @param array $notes Tableau des notes.
     */
    public function setNotesDonnees(array $notes): void
    {
        $this->notesDonnees = $notes;
    }

    /**
     * @brief Définit les notes reçues.
     * @param array $notes Tableau des notes.
     */
    public function setNotesRecues(array $notes): void
    {
        $this->notesRecues = $notes;
    }

    /**
     * @brief Récupère le nombre de tentatives de connexion échouées.
     * @return int Le nombre de tentatives.
     */
    public function getTentativesEchouees(): int
    {
        return $this->tentativesEchouees;
    }

    /**
     * @brief Définit le nombre de tentatives de connexion échouées.
     * @param int $tentatives Le nombre de tentatives.
     */
    public function setTentativesEchouees(int $tentatives): void
    {
        $this->tentativesEchouees = $tentatives;
    }

    /**
     * @brief Récupère la date du dernier échec de connexion.
     * @return string|null La date.
     */
    public function getDateDernierEchecConnexion(): ?string
    {
        return $this->dateDernierEchecConnexion;
    }

    /**
     * @brief Définit la date du dernier échec de connexion.
     * @param string|null $date La date.
     */
    public function setDateDernierEchecConnexion(?string $date): void
    {
        $this->dateDernierEchecConnexion = $date;
    }

    /**
     * @brief Récupère le statut du compte.
     * @return string|null Le statut.
     */
    public function getStatutCompte(): ?string
    {
        return $this->statutCompte;
    }

    /**
     * @brief Définit le statut du compte.
     * @param string|null $statut Le statut.
     */
    public function setStatutCompte(?string $statut): void
    {
        $this->statutCompte = $statut;
    }

    /**
     * @brief Lie une note écrite par l'utilisateur.
     * @param Note $note La note à lier.
     * @param Utilisateur|null $receveur L'utilisateur recevant la note.
     * @param Annonce|null $annonce L'annonce concernée.
     */
    public function lierNoteEcrite(Note $note, ?Utilisateur $receveur, ?Annonce $annonce)
    {
        $this->notesDonnees[] = $note;
        $receveur->notesRecues[] = $note;
        $note->setAuteur($this);
        $note->setReceveur($receveur);
        $note->setAnnonce($annonce);
    }

    /**
     * @brief Délie une note.
     * @param Note $note La note à délier.
     */
    public function delierNote(Note $note)
    {
        if (in_array($note, $this->getNotesDonnees())) { //si la note est dans les notes données
            $this->setNotesDonnees(array_filter($this->getNotesDonnees(), fn($f) => $f !== $note)); //supression liason entre note et receveur(qui n'est pas this)
            $note->getReceveur()->setNotesRecues(array_filter($note->getReceveur()->getNotesRecues(), fn($f) => $f !== $note)); //supression liason entre note et this
        } elseif (in_array($note, $this->getNotesRecues())) { //si la note est dans les notes reçues
            $note->getAuteur()->setNotesDonnees(array_filter($note->getAuteur()->getNotesDonnees(), fn($f) => $f !== $note)); //supression liason entre note et auteur(qui n'est pas this)
            $this->setNotesRecues(array_filter($this->getNotesRecues(), fn($f) => $f !== $note)); //supression liason entre note et this
        }
        $note->setAuteur(null);
        $note->setReceveur(null);
        $note->setAnnonce(null);
    }

    /**
     * @brief Calcule la moyenne des notes reçues.
     * @return float La moyenne des notes.
     */
    public function calculerMoyenneNotes(): float
    {
        $total = 0;
        $count = count($this->notesRecues);
        if ($count === 0) {
            return 0.0;
        }
        foreach ($this->notesRecues as $note) {
            $total += $note->getValeur();
        }
        return $total / $count;
    }

    /**
     * @brief Récupère l'utilisateur actuellement connecté depuis la session.
     * @return Utilisateur|null L'utilisateur connecté ou null.
     */
    public static function getUser(): ?Utilisateur
    {
        if (isset($_SESSION['id'])) {
            $bd = Bd::getInstance();
            $pdo = $bd->getConnexion();
            $userDao = new UtilisateurDao($pdo);
            $user = $userDao->findById($_SESSION['id']);
            if ($user) {
                $user->setMdp("");
            }
            return $user;
        }
        return null;
    }
}
?>