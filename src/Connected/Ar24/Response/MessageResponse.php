<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for simple messages.
 */
class MessageResponse extends StatusResponse
{
    /**
     * @var string|null
     */
    protected $message;

    /**
     * Constructor.
     *
     * @param string $status Status.
     * @param array  $data   Data.
     */
    public function __construct(string $status, array $data)
    {
        parent::__construct($data);

        $this->message = $data['message'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
