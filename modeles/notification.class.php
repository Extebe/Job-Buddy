<?php
require_once "include.php";

/**
 * @brief Classe représentant les notifications.
 */
class Notification{
    /** @var int|null $id Identifiant unique de la notification. */
    private ?int $id;
    /** @var string|null $msg Message de la notification. */
    private ?string $msg;
    /** @var int|null $idAnnonce Identifiant de l'annonce associée à la notification. */
    private ?int $idAnnonce;
    /** @var int|null $idDestinataire Identifiant de l'utilisateur destinataire de la notification. */
    private ?int $idDestinataire;

    /**
     * @brief Constructeur de la classe Notification.
     * @param int|null $id Identifiant de la notification.
     * @param string|null $msg Message de la notification.
     * @param int|null $idAnnonce Identifiant de l'annonce associée.
     * @param int|null $idDestinataire Identifiant du destinataire.
     */
    public function __construct(?int $id=null, ?string $msg=null, ?int $idAnnonce = null, ?int $idDestinataire = null)
    {
        $this->id = $id;
        $this->msg = $msg;
        $this->idAnnonce = $idAnnonce;
        $this->idDestinataire = $idDestinataire;
    }

    /* --- GETTERS --- */

    /**
     * @brief Récupère l'identifiant de la notification.
     * @return int|null L'identifiant de la notification.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Récupère le message de la notification.
     * @return string|null Le message de la notification.
     */ 
    public function getMsg(): ?string
    {
        return $this->msg;
    }

    /**
     * @brief Récupère l'identifiant de l'annonce associée à la notification.
     * @return int|null L'identifiant de l'annonce associée.
     */ 
    public function getIdAnnonce(): ?int
    {
        return $this->idAnnonce;
    }

    /**
     * @brief Récupère l'identifiant de l'utilisateur destinataire de la notification.
     * @return int|null L'identifiant du destinataire de la notification.
     */   
    public function getIdDestinataire(): ?int 
    {
        return $this->idDestinataire;
    }


    /* --- SETTERS --- */

    /**
     * @brief Définit l'identifiant de la notification.
     * @param int $id L'identifiant à définir.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @brief Définit le message de la notification.
     * @param string $msg Le message à définir.
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @brief Définit l'identifiant de l'annonce associée à la notification.
     * @param int $idAnnonce L'identifiant de l'annonce à définir.
     */
    public function setIdAnnonce(int $idAnnonce): void
    {
        $this->idAnnonce = $idAnnonce;
    }

    /**
     * @brief Définit l'identifiant de l'utilisateur destinataire de la notification.
     * @param int $idDestinataire L'identifiant du destinataire à définir.
     */
    public function setIdDestinataire(int $idDestinataire): void
    {
        $this->idDestinataire = $idDestinataire;
    }

    

}
?>