<?php

require_once "include.php";

/**
 * @brief DAO pour la gestion des utilisateurs en base de données.
 */
class UtilisateurDAO
{
    /**
     * @var PDO|null $pdo Instance de connexion PDO.
     */
    protected ?PDO $pdo;

    /**
     * @brief Constructeur de la classe UtilisateurDAO.
     * @param PDO|null $pdo Instance de connexion PDO (optionnel).
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'instance PDO associée.
     * @return PDO|null L'instance PDO ou null.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'instance PDO.
     * @param mixed $pdo Nouvelle instance PDO.
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }



    /**
     * @brief Hydrate un objet Utilisateur à partir d'un tableau associatif.
     * @param array $tableau Tableau de données de l'utilisateur.
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
     * @brief Trouve un utilisateur par son identifiant.
     * @param string|null $id Identifiant de l'utilisateur.
     * @return Utilisateur|null L'utilisateur correspondant ou null si non trouvé.
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