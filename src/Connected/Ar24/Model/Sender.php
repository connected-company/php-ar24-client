<?php declare(strict_types=1);

namespace Connected\Ar24\Model;

use Connected\Ar24\Exception\Ar24ClientException;

/**
 * Sender model.
 */
class Sender
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string|null
     */
    private $otpCode;

    /**
     * Constructor.
     *
     * @param string      $email   Sender's email.
     * @param string      $token   Sender's token.
     * @param string|null $otpCode OTP Code.
     */
    public function __construct(string $email, string $token, string $otpCode = null)
    {
        $this->setEmail($email);
        $this->setToken($token);
        $this->setOtpCode($otpCode);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getOtpCode(): ?string
    {
        return $this->otpCode;
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

    /**
     * Set and validate the token.
     *
     * @param string $token Token access.
     *
     * @throws Ar24ClientException Invalid token.
     *
     * @return self
     */
    private function setToken(string $token): self
    {
        if (empty($token)) {
            throw new Ar24ClientException('Token is invalid. The token can\'t be empty.', 500);
        }

        $this->token = $token;

        return $this;
    }

    /**
     * Set and validate the OTP code.
     *
     * @param string|null $otpCode OTP Code.
     *
     * @return self
     */
    private function setOtpCode(?string $otpCode): self
    {
        if (!is_null($otpCode) && empty($otpCode)) {
            throw new Ar24ClientException('OTP code is invalid. The OTP code can\'t be empty.', 500);
        }

        $this->otpCode = $otpCode;

        return $this;
    }
}
