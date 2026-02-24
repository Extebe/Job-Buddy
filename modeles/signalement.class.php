<?php

require_once "include.php";

/**
 * @brief Classe abstraite représentant un signalement générique.
 */
class Signalement
{       
        /** @var int|null $id Identifiant unique du signalement. */
        private ?int $id;
        /** @var Utilisateur|null $signaleur L'utilisateur ayant effectué le signalement. */
        private ?Utilisateur $signaleur;
        /** @var string|null $dateSignalement Date du signalement. */
        private ?string $dateSignalement;
        /** @var string|null $motif Motif du signalement. */
        private ?string $motif;
        /** @var string|null $description Description détaillée. */
        private ?string $description;
        /** @var string|null $description L'utilisateur signalé. */
        private ?Utilisateur $utilisateurSignale;
        /** @var string|null $description L'annonce signalé. */
        private ?Annonce $annonceSignale;

        /**
         * @brief Constructeur de la classe Signalement.
         * @param Utilisateur|null $signaleur Signaleur.
         * @param string|null $dateSignalement Date.
         * @param string|null $motif Motif.
         * @param string|null $description Description.
         * @param string|null $utilisateurSignale utilisateurSignale.
         * @param string|null $annonceSignale annonceSignale.
         */
        public function __construct(?int $id=null,
                                    ?string $dateSignalement = null, 
                                    ?string $motif = null, 
                                    ?string $description = null,
                                    ?Utilisateur $signaleur = null,
                                    ?Utilisateur $utilisateurSignale=null,
                                    ?Annonce $annonceSignale=null)
        {
                $this->id=$id;
                $this->signaleur = $signaleur;
                $this->dateSignalement = $dateSignalement;
                $this->motif = $motif;
                $this->description = $description;
                $this->utilisateurSignale=$utilisateurSignale;
                $this->annonceSignale=$annonceSignale;
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
        private function setSignaleur(?Utilisateur $signaleur): void
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
        private function setDateSignalement(?string $dateSignalement): void
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
        private function setMotif(?string $motif): void
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
        private function setDescription(?string $description): void
        {
                $this->description = $description;
        }

        /**
         * @brief Récupère l'utilisateur signale.
         * @return string|null $utilisateurSignale L'utilisateur signale.
         */
        public function getUtilisateurSignale(): ?Utilisateur
        {
                return $this->utilisateurSignale;
        }

        /**
         * @brief Définit l'utilisateur signale'.
         * @param string|null $utilisateurSignale L'utilisateur signale.
         */
        private function setUtilisateurSignale(?Utilisateur $utilisateurSignale): void
        {
                $this->utilisateurSignale = $utilisateurSignale;
        }

        /**
         *@brief Récupère l'annonce signale.
         * @return string|null $annonceSignale L'annonce signale.
         */
        private function getAnnonceSignale(): ?Annonce
        {
                return $this->annonceSignale;
        }

        /**
         *  @brief Définit l'annonce signale'.
         * @param string|null $annonceSignale L'annonce signale.
         */
        private function setAnnonceSignale(?Annonce $annonceSignale): void
        {
                $this->annonceSignale = $annonceSignale;
        }

        /**
         *@brief Récupère l'id du signalement.
         * @return int|null $id L'id du signalement.
         */
        public function getId(): ?int
        {
                return $this->id;
        }

        /**
         * @brief Définit l'id du signalement'.
         * @param string|null $id L'id du signalement.
         */
        private function setId(?int $id): void
        {
                $this->id = $id;
        }
}
?>