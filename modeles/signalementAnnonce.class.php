<?php

require_once "include.php";

/**
 * @brief Classe représentant le signalement d'une annonce.
 */
class SignalementAnnonce extends Signalement
{
    /** @var Annonce|null $annonceSignale L'annonce signalée. */
    private ?Annonce $annonceSignale;

    /**
     * @brief Constructeur de la classe SignalementAnnonce.
     * @param Utilisateur|null $signaleur Signaleur.
     * @param string|null $dateSignalement Date.
     * @param string|null $motif Motif.
     * @param string|null $description Description.
     * @param Annonce|null $annonceSignale Annonce signalée.
     */
    public function __construct(?Utilisateur $signaleur = null, ?string $dateSignalement = null, ?string $motif = null, ?string $description = null, ?Annonce $annonceSignale = null)
    {
        $this->signaleur = $signaleur;
        $this->dateSignalement = $dateSignalement;
        $this->motif = $motif;
        $this->description = $description;
        $this->annonceSignale = $annonceSignale;
    }

    /**
     * @brief Récupère l'annonce signalée.
     * @return Annonce|null L'annonce.
     */
    public function getAnnonceSignale(): ?Annonce
    {
        return $this->annonceSignale;
    }

    /**
     * @brief Définit l'annonce signalée.
     * @param Annonce|null $annonceSignale L'annonce.
     */
    public function setAnnonceSignale(?Annonce $annonceSignale): void
    {
        $this->annonceSignale = $annonceSignale;
    }
}

?>