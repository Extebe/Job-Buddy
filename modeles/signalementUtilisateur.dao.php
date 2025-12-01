<?php

    require_once "include.php";
    class SignalementUtilisateurDao extends SignalementDao{
        public function findAll(){
            $sql="SELECT * FROM signalementUtilisateur SU 
                  INNER JOIN signalement S ON SU.idSignalement=S.id
                  INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id";
            $pdoStatement = $this->getPdo()->prepare($sql);
            $pdoStatement->execute();
            $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"signalementUtilisateur");
            
            $signalement=$pdoStatement->fetchAll();
            return $signalement;
        }
        public function find(?int $id):SignalementUtilisateur{
            $sql="SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id
            WHERE  SU.idUtilisateurSignale =:id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute(array("id"=>$id));
            $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,"signalementUtilisateur");
            
            $signalement=$pdoStatement->fetch();
            return $signalement;
        }
        public function findAllAssoc(){
            $sql="SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute();
            $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
            
            $signalement=$pdoStatement->fetchAll();
            return $signalement;
        }
        public function findAssoc(?int $id):SignalementUtilisateur{
            $sql="SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id
            WHERE  SU.idUtilisateurSignale =:id";
            $pdoStatement=$this->getPdo()->prepare($sql);
            $pdoStatement->execute(array("id"=>$id));
            $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
            
            $signalement=$pdoStatement->fetch();
            return $signalement;
        }

        public function hydrate($tableauAssoc):?SignalementUtilisateur{
            $signalementUtilisateur = new SignalementUtilisateur($tableauAssoc['signaleur'],$tableauAssoc['dateSignalement'],$tableauAssoc['motif'],$tableauAssoc['description'],$tableauAssoc['utilisateurSignale']);
            return $signalementUtilisateur;
        }
        public function hydrateAll($tab): ?array{
            $listeUtilisateurSignale=[];
            foreach($tab as $tabAssoc){
                $utilisateur=$this->hydrate($tabAssoc);
                $listeUtilisateurSignale[]=$utilisateur;
            }
            return $listeUtilisateurSignale;
        }
        
    }
    
?>