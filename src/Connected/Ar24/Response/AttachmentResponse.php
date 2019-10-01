<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Attachment model.
 */
class AttachmentResponse
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $url;

    /**
     * Constructor.
     *
     * @param array $data Response data.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->url = $data['download_url'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
