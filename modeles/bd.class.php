<?php

require_once "include.php";

/**
 * @brief Singleton pour gérer la connexion à la base de données.
 */
class Bd
{
    /**
     * @var Bd|null $instance Instance unique de la classe.
     */
    private static ?Bd $instance = null;

    /**
     * @var PDO|null $pdo Instance de connexion PDO.
     */
    private ?PDO $pdo;

    /**
     * @brief Constructeur privé pour empêcher l'instanciation directe.
     * Initialise la connexion à la base de données.
     */
    private function __construct()
    {
        try {
            $constantesDB = Constantes::getConstantes()['database'];
            $this->pdo = new PDO('mysql:host=' . $constantesDB['host'] . ';dbname=' . $constantesDB['name'] . ';charset=' . $constantesDB['charset'], $constantesDB['user'], $constantesDB['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * @brief Récupère l'instance unique de la classe Bd.
     * @return Bd L'instance unique de Bd.
     */
    public static function getInstance(): Bd
    {
        if (self::$instance == null) {
            self::$instance = new Bd();
        }
        return self::$instance;
    }

    /**
     * @brief Récupère la connexion PDO.
     * @return PDO L'objet connexion PDO.
     */
    public function getConnexion(): PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Empêche le clonage de l'objet (Singleton).
     */
    private function __clone()
    {
    }

    /**
     * @brief Empêche la désérialisation de l'objet (Singleton).
     * @throws Exception Si on tente de désérialiser l'objet.
     * @bug Le nom de la méthode devrait être __wakeup au lieu de __wakup.
     */
    public function __wakup()
    {
        throw new Exception("La BD ne doit pas être deserialisée (Singleton)");
    }
}
;