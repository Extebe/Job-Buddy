<?php
require_once "include.php";

class NewLetterDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    /**
     * Get the value of pdo
     * @return  self
     */ 
    public function getPdo():?PDO
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     *
     */ 
    public function setPdo($pdo):void
    {
        $this->pdo = $pdo;
    }

    public function insererEmail(?NewLetter $newsLetter):void{
        $sql = "INSERT INTO InscritNewsLetter (email)
                VALUE (:email)";
        $pdoStatement=$this->pdo->prepare($sql);
        $pdoStatement->execute([':email'=>$newsLetter->getEmail()]);
    }
    public function emailExisteNewsletter($email) {
        // Connexion à la base de données
        $baseDeDonnees = Bd::getInstance();

        // Préparation de la requête pour vérifier si l'email existe
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'SELECT COUNT(*) FROM InscritNewsLetter WHERE email = :email'
        );

        // Exécution de la requête avec l'email récupéré au niveau du formulaire
        $requete->execute(['email' => $email]);

        // Retourne vrai si un utilisateur avec cet email existe, faux sinon
        return $requete->fetchColumn() > 0;
    }
}