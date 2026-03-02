<?php 
require_once "include.php";

/*
 * @brief DAO pour la gestion des notifications.
 */
class NotificationDao{
    /** @var  PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Notification.
     * @param PDO|null $pdo Instance de PDO.
     */
    public function __construct(?PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
?>