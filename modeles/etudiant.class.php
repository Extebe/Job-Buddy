<?php

require_once "include.php";

/**
 * @brief Classe représentant un étudiant, héritant de Utilisateur.
 */
class Etudiant extends Utilisateur
{
    /** @var string|null $codeINE Code INE de l'étudiant. */
    private ?string $codeINE;
    /** @var string|null $cvec Numéro CVEC. */
    private ?string $cvec;
    /** @var array $annoncesPostule Annonces auxquelles l'étudiant a postulé. */
    private array $annoncesPostule = [];

    /**
     * @brief Constructeur de la classe Etudiant.
     * @param int|null $id Identifiant.
     * @param string|null $codeINE Code INE.
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
     * @param string|null $cvec Numéro CVEC.
     */
    public function __construct(
        ?int $id = null,
        ?string $codeINE = null,
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
        ?string $dateSuppression = null,
        ?string $cvec = null
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
        $this->setCodeINE($codeINE);
        $this->setCvec($cvec);
    }

    /**
     * @brief Récupère le numéro CVEC.
     * @return string|null Le numéro CVEC.
     */
    public function getCvec()
    {
        return $this->cvec;
    }

    /**
     * @brief Définit le numéro CVEC.
     * @param string|null $cvec Le numéro CVEC.
     */
    public function setCvec($cvec)
    {
        $this->cvec = $cvec;
    }

    /**
     * @brief Récupère le code INE.
     * @return string|null Le code INE.
     */
    public function getCodeINE()
    {
        return $this->codeINE;
    }

    /**
     * @brief Définit le code INE.
     * @param string|null $codeINE Le code INE.
     */
    public function setCodeINE($codeINE)
    {
        $this->codeINE = $codeINE;
    }


    /**
     * @brief Vérifie la validité du CVEC via l'API officielle.
     * @param string $cvec Numéro CVEC.
     * @param string $nomComplet Nom complet de l'étudiant.
     * @param string $ineAttendu Code INE attendu.
     * @return bool True si validé, sinon False.
     */
    public function verifierCvecAvecINE(string $cvec, string $nomComplet, string $ineAttendu): bool
    {
        // Normalisation des entrées
        $cvec = strtoupper(trim($cvec));
        $ineAttendu = strtoupper(trim($ineAttendu));

        // Extraction des 5 premières lettres du nom
        $nomComplet = strtoupper(trim($nomComplet));
        $nomDebut = substr(preg_replace('/[^A-Z]/', '', $nomComplet), 0, 5);

        if (empty($nomDebut)) {
            return false;
        }

        // Construction de l'URL avec query parameter
        $url = sprintf(
            'https://cvec-ctrl.etudiant.gouv.fr/api/attestation/%s?etudiant=%s',
            urlencode($cvec),
            urlencode($nomDebut)
        );

        // Appel API avec gestion d'erreur
        try {
            $response = $this->appelApiCvec($url);

            if ($response === null) {
                return false;
            }

            // Vérification du code d'état CVEC
            return $this->estCodeValide($response['etat']['code'] ?? null);

        } catch (\Exception $e) {
            // Log de l'erreur si nécessaire
            // $this->logger->error('Erreur vérification CVEC', ['exception' => $e]);
            return false;
        }
    }

    /**
     * @brief Vérifie si le code retourné par l'API est valide.
     * @param int|null $code Code retourné.
     * @return bool True si valide, False sinon.
     */
    private function estCodeValide(?int $code): bool
    {
        $codesValides = [70, 71, 73, 75, 76, 98];
        return $code !== null && in_array($code, $codesValides, true);
    }


    /**
     * @brief Effectue un appel API pour vérifier le CVEC.
     * @param string $url URL de l'API.
     * @return array|null Réponse décodée ou null.
     */
    private function appelApiCvec(string $url): ?array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_FAILONERROR => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $data = json_decode($response, true);

        return is_array($data) ? $data : null;
    }
}