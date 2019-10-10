<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

use Connected\Ar24\Model\User;

/**
 * User configuration with identifier.
 */
class UserConfiguration extends User
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \DateTime|null
     */
    protected $expirationDate;

    /**
     * @var string|null
     */
    protected $hash;

    /**
     * Constructor.
     *
     * @param User   $user User.
     * @param string $id   User's identifier.
     */
    public function __construct(User $user, string $id)
    {
        parent::__construct($user->getEmail(), $user->getToken(), $user->getOtpCode());
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpirationDate(): ?\DateTime
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate Expiration date.
     *
     * @return self
     */
    public function setExpirationDate(\DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @param string $hash Hash.
     *
     * @return self
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }
}
