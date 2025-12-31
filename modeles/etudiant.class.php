<?php

require_once "include.php";

class Etudiant extends Utilisateur{
    private ?string $codeINE;
    private ?string $cvec;
    private array $annoncesPostule = [];

    public function __construct(
            ?int $id=null, 
            ?string $codeINE=null,
            ?string $nom=null, 
            ?string $prenom=null, 
            ?string $tel=null, 
            ?string $dateNaiss=null,
            ?string $role=null,
            ?string $email=null, 
            ?string $mdp=null, 
            ?string $adresse=null, 
            ?string $ville=null, 
            ?string $codePostal=null, 
            ?string $dateSuppression=null,
            ?string $cvec=null
        )
    {
        parent::__construct(
            $id, 
            $nom, 
            $prenom, 
            $tel, 
            $dateNaiss,
            $email, 
            $role,
            $mdp, 
            $adresse, 
            $ville, 
            $codePostal, 
            $dateSuppression);
        $this->setCodeINE($codeINE);
        $this->setCvec($cvec);
    }

    public function getCvec(){
        return $this->cvec;
    }

    public function setCvec($cvec){
        $this->cvec = $cvec;
    }

    /**
     * Get the value of codeEtudiant
     */ 
    public function getCodeINE()
    {
        return $this->codeINE;
    }

    /**
     * Set the value of codeEtudiant
     */ 
    public function setCodeINE($codeINE)
    {
        $this->codeINE = $codeINE;
    }

    

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

private function estCodeValide(?int $code): bool
{
    $codesValides = [70, 71, 73, 75, 76, 98];
    return $code !== null && in_array($code, $codesValides, true);
}


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