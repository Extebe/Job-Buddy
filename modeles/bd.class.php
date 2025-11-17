<?php

require_once "include.php";

class Bd {
    private static ?Bd $instance = null;
    private ?PDO $pdo;
    private $constantes;

    private function __construct($constantes) {
        try {
            $this->constantes = $constantes;
            $this->pdo = new PDO('mysql:host=' . $constantes['database']['host'] . ';dbname=' . $constantes['database']['name'], $constantes['database']['user'], $constantes['database']['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance($constantes): Bd {
        if (self::$instance == null) {
            self::$instance = new Bd($constantes);
        }
        return self::$instance;
    }

    public function getConnexion(): PDO {
        return $this->pdo;
    }

    // Empecher de cloner l'objet
    private function __clone() {}

    // Empecher de deserialiser l'objet (reconstruire un objet à partir d’une chaîne ou d’un format stocké)
    public function __wakup() {
        throw new Exception("La BD ne doit pas être deserialisée (Singleton)");
    }
};