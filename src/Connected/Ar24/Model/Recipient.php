<?php declare(strict_types=1);

namespace Connected\Ar24\Model;

use Connected\Ar24\Exception\Ar24ClientException;

/**
 * Recipient model.
 */
class Recipient
{
    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string|null
     */
    private $company;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * Constructor.
     *
     * @param string      $firstname Recipient's firstname.
     * @param string      $lastname  Recipient's lastname.
     * @param string      $email     Recipient's email.
     * @param string|null $company   Recipient's company.
     * @param string|null $reference Recipient's reference.
     */
    public function __construct(
        string $firstname,
        string $lastname,
        string $email,
        ?string $company = null,
        ?string $reference = null
    ) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->company = $company;
        $this->reference = $reference;
        $this->setEmail($email);
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @return boolean
     */
    public function getStatus(): string
    {
        return empty($this->company) ? 'particulier' : 'professionnel';
    }

    /**
     * Set and validate the email.
     *
     * @param string $email API email.
     *
     * @throws Ar24ClientException Invalid email.
     *
     * @return self
     */
    private function setEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Ar24ClientException('The email is invalid', 500);
        }

        $this->email = $email;

        return $this;
    }
}
