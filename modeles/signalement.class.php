<?php

    require_once "include.php";
    class Signalement{
        private ?Utilisateur $signaleur;
        private ?string $dateSignalement;
        private ?string $motif;
        private ?string $description;

        public function __construct(?Utilisateur $signaleur=null, ?string $dateSignalement=null, ?string $motif=null, ?string $description=null){
                $this->signaleur=$signaleur;
                $this->dateSignalement=$dateSignalement;
                $this->motif=$motif;
                $this->description=$description;
        }


        // GETTER ET SETTER DE SIGNALEUR
        /**
         * Get the value of signaleur
         *
         */
        public function getSignaleur(): ?Utilisateur
        {
                return $this->signaleur;
        }
        /**
         * Set the value of signaleur
         *
         */
        public function setSignaleur(?Utilisateur $signaleur): void
        {
                $this->signaleur = $signaleur;
        }



        // GETTER ET SETTER DE DATESIGNALEMENT
        /**
         * Get the value of dateSignalement
         *
         */
        public function getDateSignalement(): ?string
        {
                return $this->dateSignalement;
        }
        /**
         * Set the value of dateSignalement
         *
         */
        public function setDateSignalement(?string $dateSignalement): void
        {
                $this->dateSignalement = $dateSignalement;
        }


        //GETTEUR ET SETTEUR DE MOTIF
        /**
         * Get the value of motif
         *
         */
        public function getMotif(): ?string
        {
                return $this->motif;
        }
        /**
         * Set the value of motif
         *
         */
        public function setMotif(?string $motif): void
        {
                $this->motif = $motif;  
        }


        
        // GETTER ET SETTER DE DESCRIPTION
        /**
         * Get the value of description
         *
         */
        public function getDescription(): ?string
        {
                return $this->description;
        }

        /**
         * Set the value of description
         *
         */
        public function setDescription(?string $description): void
        {
                $this->description = $description;
        }
    }
?>
