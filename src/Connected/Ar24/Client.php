<?php declare(strict_types=1);

namespace Connected\Ar24;

use Connected\Ar24\Component\AccessPoints;
use Connected\Ar24\Component\Configuration;
use Connected\Ar24\Component\HttpClient;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\Email;
use Connected\Ar24\Response\AttachmentUploadedResponse;
use Connected\Ar24\Response\EmailResponse;
use Connected\Ar24\Response\MessageResponse;
use OTPHP\TOTP;

/**
 * Client for AR24.
 */
class Client
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param Configuration $configuration Parameters for the client.
     */
    public function __construct(Configuration $configuration)
    {
        $this->httpClient = new HttpClient(
            $configuration->getSender(),
            $configuration->getBaseUri(),
            $configuration->getWebhook(),
            $configuration->getTimeout()
        );

        $this->configuration = $configuration;
    }

    /**
     * Get informations about a simple registered email.
     *
     * @param string $id Identifier.
     *
     * @return EmailResponse
     */
    public function getEmailInformations(string $id): EmailResponse
    {
        $response = $this->httpClient->get(AccessPoints::GET_EMAIL_INFORMATIONS, ['id' => $id]);

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Send a simple registered email.
     *
     * @param Email $email Email model.
     *
     * @return EmailResponse
     */
    public function sendSimpleRegisteredEmail(Email $email): EmailResponse
    {
        $response = $this->httpClient->post(AccessPoints::SEND_EMAIL, $this->getEmailData($email));

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Send an eIDAS email.
     *
     * @param Email $email Email model.
     *
     * @return EmailResponse
     */
    public function sendEidasEmail(Email $email): EmailResponse
    {
        $response = $this->httpClient->post(AccessPoints::SEND_EMAIL, $this->getEmailData($email, true));

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Upload a document.
     *
     * @param Attachment $attachment Attachment.
     *
     * @return AttachmentUploadedResponse.
     */
    public function uploadAttachment(Attachment $attachment): AttachmentUploadedResponse
    {
        $response = $this->httpClient->post(AccessPoints::POST_ATTACHMENT, [], $attachment);

        return new AttachmentUploadedResponse($response['status'], $response['result']);
    }

    /**
     * Prepare data for an email.
     *
     * @param Email   $email Email model.
     * @param boolean $eidas Send as an eIDAS email.
     *
     * @return array
     */
    private function getEmailData(Email $email, bool $eidas = false): array
    {
        $data = [
            'to_lastname' => $email->getRecipient()->getLastname(),
            'to_firstname' => $email->getRecipient()->getFirstname(),
            'to_company' => $email->getRecipient()->getCompany(),
            'to_email' => $email->getRecipient()->getEmail(),
            'dest_statut' => $email->getRecipient()->getStatus(),
            'ref_client' => $email->getRecipient()->getReference(),
            'content' => $email->getContent(),
            'ref_dossier' => $email->getReferenceDossier(),
            'ref_facturation' => $email->getReferenceFacturation()
        ];

        foreach ($email->getAttachments() as $key => $attachment) {
            $data['attachment[' . $key . ']'] = $this->uploadAttachment($attachment)->getId();
        }

        if ($eidas) {
            if ($this->configuration->getSender()->getOtpCode()) {
                if (empty($this->configuration->getSender()->getOtpCode())) {
                    throw new Ar24ClientException('OTP code is empty', 500);
                }
            }

            $data['eidas'] = true;
            $data['otp'] = TOTP::create($this->configuration->getSender()->getOtpCode())->now();
        }

        return $data;
    }
}
