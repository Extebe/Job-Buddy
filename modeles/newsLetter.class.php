<?php
require_once "include.php";

/**
 * @brief Classe représentant une inscription à la newsletter.
 */
class NewLetter
{
    /** @var int|null $id Identifiant de l'inscription. */
    private ?int $id;
    /** @var string|null $email Email inscrit. */
    private ?string $email;

    /**
     * @brief Constructeur de la classe NewLetter.
     * @param int|null $id Identifiant.
     * @param string|null $email Email.
     */
    public function __construct(?int $id = null, ?string $email = null)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * @brief Récupère l'identifiant.
     * @return int L'identifiant.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @brief Définit l'identifiant.
     * @param mixed $id L'identifiant.
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @brief Récupère l'email.
     * @return string L'email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @brief Définit l'email.
     * @param mixed $email L'email.
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }
}