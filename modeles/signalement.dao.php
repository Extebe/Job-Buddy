<?php

require_once "include.php";

/**
 * @brief DAO de base pour la gestion des signalements.
 */
class SignalementDao
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Signalement.
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
        ;
    }
}
?>