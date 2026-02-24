<?php

require_once "include.php";

/**
 * @brief Classe de gestion de la connexion à la base de données (Singleton).
 */
class Bd
{
    /** @var Bd|null $instance Instance unique de la classe. */
    private static ?Bd $instance = null;
    /** @var PDO $pdo Objet PDO de connexion. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur privé (Pattern Singleton).
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
     * @brief Récupère l'instance unique de la classe.
     * @return Bd L'instance.
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
     * @return PDO L'objet connexion.
     */
    public function getConnexion(): PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Empêche le clonage de l'objet.
     */
    private function __clone()
    {
    }

    /**
     * @brief Empêche la désérialisation de l'objet.
     * @throws Exception Toujours.
     */
    public function __wakeup()
    {
        throw new Exception("La BD ne doit pas être deserialisée (Singleton)");
    }
}
;