<?php

require_once "include.php";


class NoteDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }
    
    public function findAll


    public function findByUser(string $idUtilisateur): array{
        //requete
        $sql = "SELECT * FROM Note where ";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $uneNote = $pdoStatement->fetch();

    }

    public function hydrate (array $tableau): Note{
        $note = new Note();
        $setId=
    }
    
}