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
}
