<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for status.
 */
class StatusResponse
{
    /**
     * @var string
     */
    protected $status;

    /**
     * Constructor.
     *
     * @param string $status Status.
     */
    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
