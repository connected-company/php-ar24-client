<?php declare(strict_types=1);

namespace Connected\Ar24\Model;

/**
 * Simple registered email model.
 */
class SimpleRegisteredEmail
{
    /**
     * @var Recipient
     */
    private $recipient;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string|null
     */
    private $referenceDossier;

    /**
     * @var string|null
     */
    private $referenceFacturation;

    /**
     * @var array
     */
    private $attachments;

    /**
     * Constructor.
     *
     * @param Recipient $recipient Recipient.
     */
    public function __construct(
        Recipient $recipient,
        string $content,
        ?string $referenceDossier = null,
        ?string $referenceFacturation = null
    ) {
        $this->recipient = $recipient;
        $this->content = $content;
        $this->referenceDossier = $referenceDossier;
        $this->referenceFacturation = $referenceFacturation;
        $this->attachments = [];
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getReferenceDossier(): ?string
    {
        return $this->referenceDossier;
    }

    /**
     * @return string|null
     */
    public function getReferenceFacturation(): ?string
    {
        return $this->referenceFacturation;
    }

    /**
     * @param Attachment $attachment Attachment.
     *
     * @return self
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
