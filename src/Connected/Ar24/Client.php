<?php declare(strict_types=1);

namespace Connected\Ar24;

use Connected\Ar24\Component\AccessPoints;
use Connected\Ar24\Component\Configuration;
use Connected\Ar24\Component\HttpClient;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\SimpleRegisteredEmail;
use Connected\Ar24\Response\AttachmentUploadedResponse;
use Connected\Ar24\Response\MessageResponse;
use Connected\Ar24\Response\SimpleRegisteredEmailResponse;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Client for AR24.
 */
class Client
{
    /**
     * @var GuzzleClient
     */
    private $httpClient;

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
    }

    /**
     * Send a simple registered email.
     *
     * @param SimpleRegisteredEmail $simpleRegisteredEmail SimpleRegisteredEmail model.
     *
     * @return SimpleRegisteredEmailResponse
     */
    public function sendSimpleRegisteredEmail(
        SimpleRegisteredEmail $simpleRegisteredEmail
    ): SimpleRegisteredEmailResponse {
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

        $response = $this->httpClient->post(AccessPoints::SEND_SIMPLE_REGISTERED_EMAIL, $data);

        return new SimpleRegisteredEmailResponse($response['status'], $response['result']);
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
     * Get informations about a simple registered email.
     *
     * @param string $id Identifier.
     *
     * @return SimpleRegisteredEmailResponse
     */
    public function getSimpleRegisteredEmailInformations(string $id): SimpleRegisteredEmailResponse
    {
        $response = $this->httpClient->get(AccessPoints::GET_SIMPLE_REGISTERED_EMAIL_INFORMATIONS, ['id' => $id]);

        return new SimpleRegisteredEmailResponse($response['status'], $response['result']);
    }
}
