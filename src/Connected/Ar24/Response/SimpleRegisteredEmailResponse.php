<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for simple registered email.
 */
class SimpleRegisteredEmailResponse extends StatusResponse
{
    /**
     * @var integer|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $emailStatus;

    /**
     * @var RecipientResponse
     */
    protected $recipientResponse;

    /**
     * @var string|null
     */
    protected $referenceDossier;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var boolean|null
     */
    protected $sendFail;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * @var string|null
     */
    protected $proofDepotUrl;

    /**
     * @var string|null
     */
    protected $proofEnvoiUrl;

    /**
     * @var string|null
     */
    protected $proofArUrl;

    /**
     * Constructor.
     *
     * @param string $status Status.
     * @param array  $data   Data.
     */
    public function __construct(string $status, array $data)
    {
        parent::__construct($status);

        $this->id = $data['id'] ?? null;
        $this->emailStatus = $data['status'] ?? null;
        $this->referenceDossier = $data['ref_dossier'] ?? null;
        $this->date = !empty($data['date']) ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['date']) : null;
        $this->sendFail = $data['send_fail'] ?? null;
        $this->recipientResponse = new RecipientResponse($data);
        $this->proofDepotUrl = $data['proof_dp_url'] ?? null;
        $this->proofEnvoiUrl = $data['proof_ev_url'] ?? null;
        $this->proofArUrl = $data['proof_ar_url'] ?? null;

        if (isset($data['attachments_details'])) {
            foreach ($data['attachments_details'] as $attachment) {
                $this->attachments[] = new AttachmentResponse($attachment);
            }
        }
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmailStatus(): ?string
    {
        return $this->emailStatus;
    }

    /**
     * @return RecipientResponse
     */
    public function getRecipientResponse(): RecipientResponse
    {
        return $this->recipientResponse;
    }

    /**
     * @return string|null
     */
    public function getReferenceDossier(): ?string
    {
        return $this->referenceDossier;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @return boolean|null
     */
    public function isSendFail(): ?bool
    {
        return $this->sendFail;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
