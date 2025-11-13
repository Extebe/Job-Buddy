<?php

require_once "include.php";

class Particulier extends Utilisateur{
    private array $listAnnoncePublie = [];

    /**
     * Get the value of listAnnoncePublie
     */ 
    public function getListAnnoncePublie()
    {
        return $this->listAnnoncePublie;
    }

    /**
     * Set the value of listAnnoncePublie
     */ 
    private function setListAnnoncePublie($listAnnoncePublie)
    {
        $this->listAnnoncePublie = $listAnnoncePublie;
    }

    public function lierAnnoncePublie($a){
        if(!$this->exist($a)){
            $this->ajouterAnnoncePublie($a);
            $a->lierParticulier($this);
        }
    }
    public function delierAnnoncePublie($a){
        $this->retirerAnnoncePublie($a);
        $a->delierParticulier();
    }

    private function ajouterAnnoncePublie($a){$this->listAnnoncePublie[] = $a;}
    private function retirerAnnoncePublie($a): void {
        foreach ($this->listAnnoncePublie as $key => $v) {
            // comparaison par identité d’objet
            if ($v === $a) {
                unset($this->listAnnoncePublie[$key]);
                // réindexation du tableau
                $this->listAnnoncePublie = array_values($this->listAnnoncePublie);
                break;
            }
        }
    }
    public function exist($a): bool {return in_array($a, $this->getListAnnoncePublie());}

    public function __toString(): string{
        $message = $this->getId();
        if(!empty($this->getListAnnoncePublie())){
            $liste = $this->getListAnnoncePublie(); // tableau d'objets
            $listeTexte = array_map(fn($p) => $p->getId(), $liste); // utiliser une méthode qui retourne string
            $message .= " - " . implode(", ", $listeTexte);
        }
        return $message;}
}