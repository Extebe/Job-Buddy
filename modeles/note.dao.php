<?php

require_once "include.php";


/**
 * @brief DAO pour la gestion des notes en base de données.
 */
class NoteDao
{
    /** @var PDO|null $pdo Instance de connexion PDO. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur de la classe NoteDao.
     * @param PDO|null $pdo Instance de connexion PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère toutes les notes sous forme d'objets Note.
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
     * @brief Trouve les notes associées à un utilisateur (auteur ou receveur).
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
     * @param string $idReceveur ID du receveur.
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

    /**
     * @brief Hydrate un objet Note à partir d'un tableau.
     * @param mixed $tableau Tableau de données.
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
     * @brief Hydrate plusieurs notes.
     * @param array $tableau Tableau de données.
     * @return array|null Tableau d'objets Note.
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
     * @brief Insère une nouvelle note en base de données.
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