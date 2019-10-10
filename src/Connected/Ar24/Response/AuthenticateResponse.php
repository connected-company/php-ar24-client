<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for the authentication.
 */
class AuthenticateResponse extends StatusResponse
{
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
     * @param string $status Status.
     * @param array  $data   Data.
     */
    public function __construct(string $status, array $data)
    {
        parent::__construct($status);

        $this->hash = $data['hash'] ?? null;
        $this->expirationDate = !empty($data['expiration_date'])
            ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['expiration_date'])
            : null
        ;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpirationDate(): ?\DateTime
    {
        return $this->expirationDate;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
}
