<?php

    require_once "include.php";
    
    class SignalementAnnonce extends Signalement{
        private ?Annonce $annonceSignale;

        public function __construct(?Utilisateur $signaleur=null, ?string $dateSignalement=null, ?string $motif=null, ?string $description=null, ?Annonce $annonceSignale=null){
            $this->signaleur=$signaleur;
            $this->dateSignalement=$dateSignalement;
            $this->motif=$motif;
            $this->description=$description;
            $this->annonceSignale=$annonceSignale;
        }

        /**
         * Get the value of annonceSignale
         *
         */
        public function getAnnonceSignale(): ?Annonce
        {
                return $this->annonceSignale;
        }

        /**
         * Set the value of annonceSignale
         *
         */
        public function setAnnonceSignale(?Annonce $annonceSignal): void
        {
                $this->annonceSignale = $annonceSignale;
        }
    }

?>