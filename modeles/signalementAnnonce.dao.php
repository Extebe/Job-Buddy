<?php

    require_once "include.php";
    class SignalementAnnonceDoa extends Signalement{
        public function findAll(){
            $sql="SELECT * FROM signalementAnnonce SA
                  INNER JOIN signalement S ON SA.idSignalement=S.id
                  INNER JOIN annonce A ON SA.idAnnonceSignale=A.id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute();
            $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"signalementAnnonce");
            
            $signalement=$pdoStatement->fetchAll();
            return $signalement;
        }
        public function find(?int $id):?SignalementAnnonce{
            $sql="SELECT * FROM signalementAnnonce SA
            INNER JOIN signalement S ON SA.idSignalement=S.id
            INNER JOIN annonce A ON SA.idAnnonceSignale=A.id
            WHERE SA.idAnnonceSignale = :id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute(array("id"=>$id));
            $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"signalementAnnonce");
            
            $signalement=$pdoStatement->fetch();
            return $signalement;
        }
        public function findAllAssoc(){
            $sql="SELECT * FROM signalementAnnonce SA
            INNER JOIN signalement S ON SA.idSignalement=S.id
            INNER JOIN annonce A ON SA.idAnnonceSignale=A.id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute();
            $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
            
            $signalement=$pdoStatement->fetchAll();
            return $signalement;
        }
        public function findAssoc(?int $id):?SignalementAnnonce{
            $sql="SELECT * FROM signalementAnnonce SA
            INNER JOIN signalement S ON SA.idSignalement=S.id
            INNER JOIN annonce A ON SA.idAnnonceSignale=A.id
            WHERE SA.idAnnonceSignale = :id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute(array("id"=>$id));
            $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
            
            $signalement=$pdoStatement->fetch();
            return $signalement;
        }

        public function hydrate($tabAssoc): ?SignalementAnnonce{
            $signalementAnnonce=new SignalementAnnonce($tabAssoc['signaleur'],$tabAssoc['dateSignalement'],$tabAssoc['motif'],$tabAssoc['description'],$tabAssoc['annondeSignale'],);
            return $signalementAnnonce;
        }

        public function hydrateAll($tab):?array{
            $listeSignalementAnnonce=[];
            foreach ($tab as $tabAssoc) {
                $signalementAnnonce=$this->hydrate($tabAssoc);
                $listeSignalementAnnonce[]=$signalementAnnonce;
            }
            return $listeSignalementAnnonce;
        }
    }
?>