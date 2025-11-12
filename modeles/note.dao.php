<?php

require_once "include.php";


class NoteDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }
    
    public function findAll(): array{
        //requete
        $sql = "SELECT * FROM Note";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Note');
        $notes = $pdoStatement->fetchAll();

        return $notes;
    }


    public function findByUser(string $idAuteur): array{
        //requete
        $sql = "SELECT * FROM Note where idAuteur = :idAuteur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetchAll();  
        return $notes;

    }

    public function hydrate ($tableau): ?Note{
        $note = new Note();
        $note->setId($tableau['idNote'] ?? null);
        $note->setValeur($tableau['valeur'] ?? null);
        $note->setCommentaire($tableau['commentaire'] ?? null);
        $note->setAuteur($tableau['idAuteur'] ?? null);
        $note->setReceveur($tableau['idReceveur'] ?? null);
        $note->setAnnonce($tableau['idAnnonce'] ?? null);
        return $note;

    }

        public function hydrateAll($tableau): ?array{
        $note = [];
        foreach($tableau as $tableauAssoc){//tableauAssoc = chaque ligne
            $note = $this->hydrate($tableauAssoc);
            $notes[] = $note;
        }
        return $notes;
    }
    
}