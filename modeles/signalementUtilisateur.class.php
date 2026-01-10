<?php

require_once "include.php";

/**
 * @brief Classe représentant le signalement d'un utilisateur.
 */
class SignalementUtilisateur extends Signalement
{
    /** @var Utilisateur|null $utilisateurSignale L'utilisateur signalé. */
    private ?Utilisateur $utilisateurSignale;

    /**
     * @brief Constructeur de la classe SignalementUtilisateur.
     * @param Utilisateur|null $signaleur Signaleur.
     * @param string|null $dateSignalement Date.
     * @param string|null $motif Motif.
     * @param string|null $description Description.
     * @param Utilisateur|null $utilisateurSignale Utilisateur signalé.
     */
    public function __construct(?Utilisateur $signaleur = null, ?string $dateSignalement = null, ?string $motif = null, ?string $description = null, ?Utilisateur $utilisateurSignale = null)
    {
        $this->signaleur = $signaleur;
        $this->dateSignalement = $dateSignalement;
        $this->motif = $motif;
        $this->description = $description;
        $this->utilisateurSignale = $utilisateurSignale;
    }

    /**
     * @brief Récupère l'utilisateur signalé.
     * @return Utilisateur|null L'utilisateur.
     */
    public function getUtilisateurSignale(): ?Utilisateur
    {
        return $this->utilisateurSignale;
    }

    /**
     * @brief Définit l'utilisateur signalé.
     * @param Utilisateur|null $utilisateurSignale L'utilisateur.
     */
    public function setUtilisateurSignale(?Utilisateur $utilisateurSignale): void
    {
        $this->utilisateurSignale = $utilisateurSignale;
    }
}
?>