<?php

require_once "include.php";
/**
 * @brief DAO de base pour les signalements.
 */
class SignalementDao
{
    /** @var PDO|null $pdo Instance de connexion PDO. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur de la classe SignalementDao.
     * @param PDO|null $pdo Instance de connexion PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'instance PDO.
     * @return PDO|null L'instance PDO.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'instance PDO.
     * @param mixed $pdo L'instance PDO.
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }
}
?>