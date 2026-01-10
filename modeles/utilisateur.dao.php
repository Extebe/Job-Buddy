<?php

require_once "include.php";

/**
 * @brief DAO de base pour la gestion des utilisateurs.
 */
class UtilisateurDAO
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    protected ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Utilisateur.
     * @param PDO|null $pdo Instance de PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'objet PDO.
     * @return PDO|null L'objet PDO.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'objet PDO.
     * @param PDO|null $pdo L'objet PDO.
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }



    /**
     * @brief Hydrate un objet Utilisateur.
     * @param array $tableau Données de l'utilisateur.
     * @return Utilisateur L'objet utilisateur hydraté.
     */
    public function hydrate($tableau)
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setId($tableau['id'] ?? null);
        $utilisateur->setRole($tableau['role'] ?? null);
        $utilisateur->setNom($tableau['nom'] ?? null);
        $utilisateur->setPrenom($tableau['prenom'] ?? null);
        $utilisateur->setTel($tableau['tel'] ?? null);
        $utilisateur->setDateNaiss($tableau['dateNaiss'] ?? null);
        $utilisateur->setEmail($tableau['email'] ?? null);
        $utilisateur->setMdp($tableau['mdp'] ?? null);
        $utilisateur->setVille($tableau['ville'] ?? null);
        $utilisateur->setAdresse($tableau['adresse'] ?? null);
        $utilisateur->setCodePostal($tableau['codePostal'] ?? null);

        return $utilisateur;
    }

    /**
     * @brief Trouve un utilisateur par son ID.
     * @param string|null $id ID de l'utilisateur.
     * @return Utilisateur|null L'utilisateur trouvé.
     */
    function findById(?string $id): ?Utilisateur
    {
        $sql = "SELECT * FROM Utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['id' => $id]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        return $this->hydrate($tableau);
    }
}
?>