<?php declare(strict_types=1);

namespace Connected\Ar24\Model;

/**
 * Email model.
 */
class Email
{
    /**
     * @var Recipient
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string|null
     */
    protected $referenceDossier;

    /**
     * @var string|null
     */
    protected $referenceFacturation;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * Constructor.
     *
     * @param Recipient   $recipient            Recipient.
     * @param string      $content              Recipient.
     * @param string|null $referenceDossier     Référence dossier.
     * @param string|null $referenceFacturation Référence facturation.
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
