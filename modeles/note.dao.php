<?php

require_once "include.php";


/**
 * @brief DAO pour la gestion des notes.
 */
class NoteDao
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Note.
     * @param PDO|null $pdo Instance de PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère toutes les notes sous forme d'objets.
     * @return array Tableau d'objets Note.
     */
    public function findAllAssoc(): array
    {
        //requete
        $sql = "SELECT * FROM Note";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Note');
        $notes = $pdoStatement->fetchAll();

        return $notes;
    }


    /**
     * @brief Trouve les notes associées à un utilisateur (auteur ou destinataire).
     * @param string $idAuteur ID de l'utilisateur.
     * @return array Tableau de notes (associatif).
     */
    public function findByUser(string $idAuteur): array
    {
        //requete
        $sql = "SELECT * FROM Note where idUtilisateurNote = :idAuteur OR idUtilisateurNoteur = :idAuteur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetchAll();
        return $notes;

    }

    /**
     * @brief Trouve une note entre deux utilisateurs spécifiques.
     * @param string $idAuteur ID de l'auteur.
     * @param string $idReceveur ID du destinataire.
     * @return Note|null La note trouvée ou null.
     */
    public function findByUsers(string $idAuteur, string $idReceveur): ?Note
    {
        //requete
        $sql = "SELECT * FROM Note where idUtilisateurNoteur = :idAuteur AND idUtilisateurNote = :idReceveur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur, 'idReceveur' => $idReceveur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetch();

        return $this->hydrate($notes);

    }

public function addNoteRecue(Utilisateur $user): Utilisateur
    {
        $idReceveur = $user->getId();
        
        // On ne sélectionne que la colonne 'note'
        $sql = "SELECT note FROM Note WHERE idUtilisateurNote = :idReceveur";
        
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idReceveur' => $idReceveur]);
        
        // PDO::FETCH_COLUMN te retourne un tableau simple : [5, 4, 3, 5...] 
        // (au lieu d'un tableau associatif lourd de type [['note' => 5], ['note' => 4]])
        $notes = $pdoStatement->fetchAll(PDO::FETCH_COLUMN);

        // On met à jour l'array notesRecues de l'utilisateur
        $user->setNotesRecues($notes);

        // On retourne l'utilisateur mis à jour !
        return $user;
    }

    /**
     * @brief Hydrate un objet Note.
     * @param array $tableau Données de la note.
     * @return Note|null L'objet Note hydraté.
     */
    public function hydrate($tableau): ?Note
    {
        $note = new Note();
        $note->setId($tableau['id'] ?? null);
        $note->setValeur($tableau['note'] ?? null);
        $note->setCommentaire($tableau['commentaire'] ?? null);
        //hydratation de annonce
        $annonceDAO = new AnnonceDAO($this->pdo);
        $annonce = $annonceDAO->find($tableau['idAnnonce'] ?? null);
        $note->setAnnonce($annonce ?? null);
        //hydratation des utilisateurs
        $utilisateurDAO = new UtilisateurDAO($this->pdo);
        $auteur = $utilisateurDAO->findById($tableau['idUtilisateurNoteur'] ?? null);
        $note->setAuteur($auteur ?? null);
        $receveur = $utilisateurDAO->findById($tableau['idUtilisateurNote'] ?? null);
        $note->setReceveur($receveur ?? null);

        return $note;

    }

    /**
     * @brief Hydrate une liste de notes.
     * @param array $tableau Tableau de données.
     * @return array Tableau d'objets Note.
     */
    public function hydrateAll($tableau): ?array
    {
        $notes = [];
        foreach ($tableau as $tableauAssoc) {//tableauAssoc = chaque ligne
            $note = $this->hydrate($tableauAssoc);
            $notes[] = $note;
        }
        return $notes;
    }

    /**
     * @brief Insère une nouvelle note dans la base de données.
     * @param Note $note La note à insérer.
     */
    public function insert(Note $note): void
    {
        $sql = "INSERT INTO Note (idAnnonce, idUtilisateurNoteur, idUtilisateurNote, note, commentaire) VALUES (:idAnnonce, :idUtilisateurNoteur, :idUtilisateurNote, :note, :commentaire)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([
            'note' => $note->getValeur(),
            'commentaire' => $note->getCommentaire(),
            'idUtilisateurNoteur' => $note->getAuteur() ? $note->getAuteur()->getId() : null,
            'idUtilisateurNote' => $note->getReceveur() ? $note->getReceveur()->getId() : null,
            'idAnnonce' => $note->getAnnonce() ? $note->getAnnonce()->getId() : null
        ]);
    }

}