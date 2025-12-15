<?php

require_once "include.php";


class NoteDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }
    
    public function findAllAssoc(): array{
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
        $sql = "SELECT * FROM Note where idUtilisateurNote = :idAuteur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetchAll();  
        return $notes;

    }

    public function hydrate ($tableau): ?Note{
        $note = new Note();
        $note->setId($tableau['id'] ?? null);
        $note->setValeur($tableau['note'] ?? null);
        $note->setCommentaire($tableau['commentaire'] ?? null);
        //hydratation de annonce
        $annonceDAO=new AnnonceDAO($this->pdo);
        $annonce=$annonceDAO->find($tableau['idAnnonce'] ?? null);
        $note->setAnnonce($annonce ?? null);
        //hydratation des utilisateurs
        $utilisateurDAO=new UtilisateurDAO($this->pdo);
        $auteur=$utilisateurDAO->findById($tableau['idUtilisateurNoteur'] ?? null);
        $note->setAuteur($auteur ?? null);
        $receveur=$utilisateurDAO->findById($tableau['idUtilisateurNote'] ?? null);
        $note->setReceveur($receveur ?? null);
        
        return $note;

    }

        public function hydrateAll($tableau): ?array{
        $notes = [];
        foreach($tableau as $tableauAssoc){//tableauAssoc = chaque ligne
            $note = $this->hydrate($tableauAssoc);
            $notes[] = $note;
        }
        return $notes;
    }
    
}