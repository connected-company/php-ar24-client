<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for attachment when uploaded.
 */
class AttachmentUploadedResponse extends StatusResponse
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param string $status Status.
     * @param array  $data   Data.
     */
    public function __construct(string $status, array $data)
    {
        parent::__construct($status);

        $this->id = $data['file_id'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
