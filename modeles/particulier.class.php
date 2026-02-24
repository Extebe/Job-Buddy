<?php

require_once "include.php";

/**
 * @brief Classe représentant un particulier, héritant de Utilisateur.
 */
class Particulier extends Utilisateur
{
    /** @var array $listAnnoncePublie Liste des annonces publiées par le particulier. */
    private array $listAnnoncePublie = [];

    /**
     * @brief Constructeur de la classe Particulier.
     * @param int|null $id Identifiant.
     * @param string|null $nom Nom.
     * @param string|null $prenom Prénom.
     * @param string|null $tel Téléphone.
     * @param string|null $dateNaiss Date de naissance.
     * @param string|null $role Rôle.
     * @param string|null $email Email.
     * @param string|null $mdp Mot de passe.
     * @param string|null $adresse Adresse.
     * @param string|null $ville Ville.
     * @param string|null $codePostal Code postal.
     * @param string|null $dateSuppression Date de suppression.
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $prenom = null,
        ?string $tel = null,
        ?string $dateNaiss = null,
        ?string $role = null,
        ?string $email = null,
        ?string $mdp = null,
        ?string $adresse = null,
        ?string $ville = null,
        ?string $codePostal = null,
        ?string $dateSuppression = null
    ) {
        parent::__construct(
            $id,
            $nom,
            $prenom,
            $tel,
            $dateNaiss,
            $role,
            $email,
            $mdp,
            $adresse,
            $ville,
            $codePostal,
            $dateSuppression
        );
    }

    /**
     * @brief Récupère la liste des annonces publiées.
     * @return array Tableau des annonces.
     */
    public function getListAnnoncePublie()
    {
        return $this->listAnnoncePublie;
    }

    /**
     * @brief Définit la liste des annonces publiées.
     * @param array $listAnnoncePublie Nouvelle liste.
     */
    private function setListAnnoncePublie($listAnnoncePublie)
    {
        $this->listAnnoncePublie = $listAnnoncePublie;
    }

    /**
     * @brief Lie une annonce publiée à ce particulier.
     * @param Annonce $a L'annonce à lier.
     */
    public function lierAnnoncePublie($a)
    {
        if (!$this->exist($a)) {
            $this->ajouterAnnoncePublie($a);
            // $a->lierParticulier($this); // Commented to recall recursion issue logic if needed
        }
    }

    /**
     * @brief Délit une annonce publiée.
     * @param Annonce $a L'annonce à délier.
     */
    public function delierAnnoncePublie($a)
    {
        $this->retirerAnnoncePublie($a);
        // $a->delierParticulier(); // Commented to recall recursion issue logic if needed
    }

    /**
     * @brief Ajoute une annonce à la liste locale.
     * @param Annonce $a L'annonce.
     */
    private function ajouterAnnoncePublie($a)
    {
        $this->listAnnoncePublie[] = $a;
    }

    /**
     * @brief Retire une annonce de la liste locale.
     * @param Annonce $a L'annonce.
     */
    private function retirerAnnoncePublie($a): void
    {
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

    /**
     * @brief Vérifie si une annonce existe dans la liste.
     * @param Annonce $a L'annonce.
     * @return bool True si présente.
     */
    public function exist($a): bool
    {
        return in_array($a, $this->getListAnnoncePublie());
    }

    public function __toString(): string
    {
        $message = $this->getId();
        if (!empty($this->getListAnnoncePublie())) {
            $liste = $this->getListAnnoncePublie(); // tableau d'objets
            $listeTexte = array_map(fn($p) => $p->getId(), $liste); // utiliser une méthode qui retourne string
            $message .= " - " . implode(", ", $listeTexte);
        }
        return $message;
    }
}