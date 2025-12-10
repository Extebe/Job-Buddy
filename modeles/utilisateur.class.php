<?php

require_once "include.php";

class Utilisateur{
    private ?int $id;
    private ?string $nom;
    private ?string $prenom;
    private ?string $telephone;
    private ?string $dateNaiss;
    private ?string $role;
    private ?string $codeINE;
    private ?string $email;
    private ?string $mdp;
    private ?string $adresse;
    private ?string $ville;
    private ?string $codePostal;
    private array $notesDonnees = [];
    private array $notesRecues = [];


    public function __construct(?int $id=null, ?string $nom=null, ?string $prenom=null, ?string $telephone=null, ?string $dateNaiss=null, ?string $role=null, ?string $codeINE=null, ?string $email=null, ?string $mdp=null, ?string $adresse=null, ?string $ville=null, ?string $codePostal=null){
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->dateNaiss = $dateNaiss;
        $this->role = $role;
        $this->codeINE = $codeINE;
        $this->email = $email;
        $this->mdp = $mdp;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of nom
     */ 
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     */ 
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * Get the value of prenom
     */ 
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     */ 
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * Get the value of telephone
     */ 
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     */ 
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Get the value of dateNaiss
     */ 
    public function getDateNaiss()
    {
        return $this->dateNaiss;
    }

    /**
     * Set the value of dateNaiss
     */ 
    public function setDateNaiss($dateNaiss)
    {
        $this->dateNaiss = $dateNaiss;
    }

    public function getRole(){
        return $this->role;
    }

    public function setRole($role){
        $this->role = $role;
    }

    public function getCodeINE(){
        return $this->codeINE;
    }

    public function setCodeINE($codeINE){
        $this->codeINE = $codeINE;
    }
    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */ 
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get the value of mdp
     */ 
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Set the value of mdp
     */ 
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * Get the value of adresse
     */ 
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set the value of adresse
     */ 
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * Get the value of ville
     */ 
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set the value of ville
     */ 
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * Get the value of codePostal
     */ 
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set the value of codePostal
     */ 
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    }

    public function getNotesDonnees(): array {
        return $this->notesDonnees;
    }

    public function getNotesRecues(): array {
        return $this->notesRecues;
    }

    public function setNotesDonnees(array $notes): void {
        $this->notesDonnees = $notes;
    }

    public function setNotesRecues(array $notes): void {
        $this->notesRecues = $notes;
    }

    public function lierNoteEcrite(Note $note, ?Utilisateur $receveur, ?Annonce $annonce){
        $this->notesDonnees[] = $note;
        $receveur->notesRecues[] = $note;
        $note->setAuteur($this);
        $note->setReceveur($receveur);
        $note->setAnnonce($annonce);
    }

    public function delierNote(Note $note){
        if (in_array($note,$this->getNotesDonnees())){//si la note est dans les notes données
            $this->setNotesDonnees(array_filter($this->getNotesDonnees(), fn($f) => $f !== $note));//supression liason entre note et receveur(qui n'est pas this)
            $note->getReceveur()->setNotesRecues(array_filter($note->getReceveur()->getNotesRecues(), fn($f) => $f !== $note));//supression liason entre note et this
        }
        elseif (in_array($note,$this->getNotesRecues())){//si la note est dans les notes reçues
            $note->getAuteur()->setNotesDonnees(array_filter($note->getAuteur()->getNotesDonnees(), fn($f) => $f !== $note));//supression liason entre note et auteur(qui n'est pas this)
            $this->setNotesRecues(array_filter($this->getNotesRecues(), fn($f) => $f !== $note));//supression liason entre note et this
        }
        $note->setAuteur(null);
        $note->setReceveur(null);
        $note->setAnnonce(null);
    }




    public function calculerMoyenneNotes(): float {
        $total = 0;
        $count = count($this->notesRecues);
        if ($count === 0) {
            return 0.0;
        }
        foreach ($this->notesRecues as $note) {
            $total += $note->getValeur();
        }
        return $total / $count;
    }   
}
?>