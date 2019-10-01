<?php declare(strict_types=1);

namespace Connected\Ar24;

use Connected\Ar24\Component\AccessPoints;
use Connected\Ar24\Component\Configuration;
use Connected\Ar24\Component\HttpClient;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\EidasEmail;
use Connected\Ar24\Model\SimpleRegisteredEmail;
use Connected\Ar24\Response\AttachmentUploadedResponse;
use Connected\Ar24\Response\EidasEmailResponse;
use Connected\Ar24\Response\MessageResponse;
use Connected\Ar24\Response\SimpleRegisteredEmailResponse;
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
     * @return SimpleRegisteredEmailResponse
     */
    public function getSimpleRegisteredEmailInformations(string $id): SimpleRegisteredEmailResponse
    {
        $response = $this->httpClient->get(AccessPoints::GET_EMAIL_INFORMATIONS, ['id' => $id]);

        return new SimpleRegisteredEmailResponse($response['status'], $response['result']);
    }

    /**
     * Send a simple registered email.
     *
     * @param SimpleRegisteredEmail $simpleRegisteredEmail SimpleRegisteredEmail model.
     *
     * @return SimpleRegisteredEmailResponse
     */
    public function sendSimpleRegisteredEmail(SimpleRegisteredEmail $simpleRegisteredEmail): SimpleRegisteredEmailResponse
    {
        $response = $this->httpClient->post(AccessPoints::SEND_EMAIL, $this->getEmailData($simpleRegisteredEmail));

        return new SimpleRegisteredEmailResponse($response['status'], $response['result']);
    }

    /**
     * Send an eIDAS email.
     *
     * @param EidasEmail $eidasEmail EidasEmail model.
     *
     * @return EidasEmailResponse
     */
    public function sendEidasEmail(EidasEmail $eidasEmail): EidasEmailResponse
    {
        $response = $this->httpClient->post(AccessPoints::SEND_EMAIL, $this->getEmailData($eidasEmail, true));

        return new EidasEmailResponse($response['status'], $response['result']);
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
     * @param SimpleRegisteredEmail $simpleRegisteredEmail SimpleRegisteredEmail model.
     * @param boolean               $eidas                 Send as an eIDAS email.
     *
     * @return array
     */
    private function getEmailData(SimpleRegisteredEmail $simpleRegisteredEmail, bool $eidas = false): array
    {
        $data = [
            'to_lastname' => $simpleRegisteredEmail->getRecipient()->getLastname(),
            'to_firstname' => $simpleRegisteredEmail->getRecipient()->getFirstname(),
            'to_company' => $simpleRegisteredEmail->getRecipient()->getCompany(),
            'to_email' => $simpleRegisteredEmail->getRecipient()->getEmail(),
            'dest_statut' => $simpleRegisteredEmail->getRecipient()->getStatus(),
            'ref_client' => $simpleRegisteredEmail->getRecipient()->getReference(),
            'content' => $simpleRegisteredEmail->getContent(),
            'ref_dossier' => $simpleRegisteredEmail->getReferenceDossier(),
            'ref_facturation' => $simpleRegisteredEmail->getReferenceFacturation()
        ];

        foreach ($simpleRegisteredEmail->getAttachments() as $key => $attachment) {
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
