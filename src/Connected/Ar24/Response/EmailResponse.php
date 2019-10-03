<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

/**
 * Response for email.
 */
class EmailResponse extends StatusResponse
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
     * @var \DateTime|null
     */
    protected $dateSent;

    /**
     * @var \DateTime|null
     */
    protected $dateOpened;

    /**
     * @var \DateTime|null
     */
    protected $dateRefused;

    /**
     * @var \DateTime|null
     */
    protected $dateExpired;

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
        $this->dateSent = !empty($data['ts_ev_date']) ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['ts_ev_date']) : null;
        $this->dateOpened = !empty($data['view_date']) ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['view_date']) : null;
        $this->dateRefused = !empty($data['refused_date']) ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['refused_date']) : null;
        $this->dateExpired = !empty($data['negligence_date']) ? (new \DateTime())->createFromFormat('Y-m-d H:i:s', $data['negligence_date']) : null;
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
     * @return \DateTime|null
     */
    public function getDateSent(): ?\DateTime
    {
        return $this->dateSent;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateOpened(): ?\DateTime
    {
        return $this->dateOpened;
    }

    /**
     * @return \DateTime|null Date.
     */
    public function getDateRefused(): ?\DateTime
    {
        return $this->dateRefused;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateExpired(): ?\DateTime
    {
        return $this->dateExpired;
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
