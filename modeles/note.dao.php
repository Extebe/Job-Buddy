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
        $sql = "SELECT * FROM Note where idUtilisateurNote = :idAuteur OR idUtilisateurNoteur = :idAuteur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetchAll();  
        return $notes;

    }

        public function findByUsers(string $idAuteur,string $idReceveur): ?Note{
        //requete
        $sql = "SELECT * FROM Note where idUtilisateurNoteur = :idAuteur AND idUtilisateurNote = :idReceveur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['idAuteur' => $idAuteur, 'idReceveur' => $idReceveur]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $notes = $pdoStatement->fetch();  

        return $this->hydrate($notes);

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
    public function insert(Note $note): void{
        $sql = "INSERT INTO Note (idAnnonce, idUtilisateurNoteur, idUtilisateurNote, note, commentaire) VALUES (:idAnnonce, :idUtilisateurNoteur, :idUtilisateurNote, :note, :commentaire)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([
            'note' => $note->getValeur(),
            'commentaire' => $note->getCommentaire(),
            'idUtilisateurNoteur' => $note->getAuteur() ? $note->getAuteur()->getId() : null,
            'idUtilisateurNote' => $note->getReceveur() ? $note->getReceveur()->getId() : null,
            'idAnnonce' => $note->getAnnonce() ? $note->getAnnonce()->getId() : null
        ]);
    }
    
}