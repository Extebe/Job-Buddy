<?php

    require_once "include.php";

    class SignalementUtilisateur extends Signalement{
        private ?Utilisateur $utilisateurSignale;

        public function __construct(?Utilisateur $signaleur=null, ?string $dateSignalement=null, ?string $motif=null, ?string $description=null, ?Utilisateur $utilisateurSignale=null){
            $this->signaleur=$signaleur;
            $this->dateSignalement=$dateSignalement;
            $this->motif=$motif;
            $this->description=$description;
            $this->utilisateurSignale=$utilisateurSignale;
        }

        /**
         * Get the value of utilisateurSignale
         */
        public function getUtilisateurSignale(): ?Utilisateur
        {
                return $this->utilisateurSignale;
        }

        /**
         * Set the value of utilisateurSignale
         */
        public function setUtilisateurSignale(?Utilisateur $utilisateurSignale): void
        {
                $this->utilisateurSignale = $utilisateurSignale;
        }
    }
?>