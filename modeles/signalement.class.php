<?php

require_once "include.php";
/**
 * @brief Classe de base représentant un signalement.
 */
class Signalement
{
        /** @var Utilisateur|null $signaleur L'utilisateur qui signale. */
        private ?Utilisateur $signaleur;
        /** @var string|null $dateSignalement Date du signalement. */
        private ?string $dateSignalement;
        /** @var string|null $motif Motif du signalement. */
        private ?string $motif;
        /** @var string|null $description Description détaillée. */
        private ?string $description;

        /**
         * @brief Constructeur de la classe Signalement.
         * @param Utilisateur|null $signaleur Signaleur.
         * @param string|null $dateSignalement Date.
         * @param string|null $motif Motif.
         * @param string|null $description Description.
         */
        public function __construct(?Utilisateur $signaleur = null, ?string $dateSignalement = null, ?string $motif = null, ?string $description = null)
        {
                $this->signaleur = $signaleur;
                $this->dateSignalement = $dateSignalement;
                $this->motif = $motif;
                $this->description = $description;
        }


        // GETTER ET SETTER DE SIGNALEUR
        /**
         * @brief Récupère le signaleur.
         * @return Utilisateur|null Le signaleur.
         */
        public function getSignaleur(): ?Utilisateur
        {
                return $this->signaleur;
        }
        /**
         * @brief Définit le signaleur.
         * @param Utilisateur|null $signaleur Le signaleur.
         */
        public function setSignaleur(?Utilisateur $signaleur): void
        {
                $this->signaleur = $signaleur;
        }



        // GETTER ET SETTER DE DATESIGNALEMENT
        /**
         * @brief Récupère la date du signalement.
         * @return string|null La date.
         */
        public function getDateSignalement(): ?string
        {
                return $this->dateSignalement;
        }
        /**
         * @brief Définit la date du signalement.
         * @param string|null $dateSignalement La date.
         */
        public function setDateSignalement(?string $dateSignalement): void
        {
                $this->dateSignalement = $dateSignalement;
        }


        //GETTEUR ET SETTEUR DE MOTIF
        /**
         * @brief Récupère le motif.
         * @return string|null Le motif.
         */
        public function getMotif(): ?string
        {
                return $this->motif;
        }
        /**
         * @brief Définit le motif.
         * @param string|null $motif Le motif.
         */
        public function setMotif(?string $motif): void
        {
                $this->motif = $motif;
        }



        // GETTER ET SETTER DE DESCRIPTION
        /**
         * @brief Récupère la description.
         * @return string|null La description.
         */
        public function getDescription(): ?string
        {
                return $this->description;
        }

        /**
         * @brief Définit la description.
         * @param string|null $description La description.
         */
        public function setDescription(?string $description): void
        {
                $this->description = $description;
        }
}
?>