<?php declare(strict_types=1);

namespace Connected\Ar24;

use Connected\Ar24\Component\AccessPoints;
use Connected\Ar24\Component\ClientTrait;
use Connected\Ar24\Component\Configuration;
use Connected\Ar24\Component\HttpClient;
use Connected\Ar24\Component\UserConfiguration;
use Connected\Ar24\Exception\Ar24ApiException;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\Email;
use Connected\Ar24\Model\User;
use Connected\Ar24\Response\AttachmentUploadedResponse;
use Connected\Ar24\Response\AuthenticateResponse;
use Connected\Ar24\Response\EmailResponse;
use Connected\Ar24\Response\MessageResponse;
use OTPHP\TOTP;

/**
 * Client for AR24.
 */
class Client
{
    use ClientTrait;
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
            $configuration->getBaseUri(),
            $configuration->getWebhook(),
            $configuration->getTimeout()
        );

        $this->configuration = $configuration;
    }

    /**
     * Add a new User.
     *
     * @param User $user User.
     *
     * @return self
     */
    public function addUser(User $user): self
    {
        $response = $this->httpClient->authenticate($user);
        $userConfiguration = new UserConfiguration($user, $response['result']['id']);
        $this->configuration->addUserConfiguration($userConfiguration);

        return $this;
    }

    /**
     * Get utilisateur identifier.
     *
     * @param User $user User.
     *
     * @return string
     */
    public function getUserId(User $user): string
    {
        return $this->getUserConfiguration($user)->getId();
    }

    /**
     * Authenticate user with TOTP code.
     *
     * @param User $user User.
     *
     * @return AuthenticateResponse|null
     */
    public function refreshOtpHash(User $user): ?AuthenticateResponse
    {
        if (!empty($this->getUserConfiguration($user)->getHash())
            && $this->getUserConfiguration($user)->getExpirationDate() > (new \DateTime())
        ) {
            return null;
        }

        $response = $this->httpClient->post(
            $this->getUserConfiguration($user),
            AccessPoints::AUTHENTICATE_OTP,
            ['otp' => TOTP::create($user->getOtpCode())->now()]
        );

        $response = new AuthenticateResponse($response['status'], $response['result']);
        $this->getUserConfiguration($user)
            ->setHash($response->getHash())
            ->setExpirationDate($response->getExpirationDate())
        ;

        return $response;
    }

    /**
     * Send a simple registered email.
     *
     * @param User  $user  User.
     * @param Email $email Email model.
     *
     * @return EmailResponse
     */
    public function sendSimpleRegisteredEmail(User $user, Email $email): EmailResponse
    {
        $response = $this->httpClient->post(
            $this->getUserConfiguration($user),
            AccessPoints::POST_EMAIL,
            $this->getEmailData($user, $email)
        );

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Send an eIDAS email.
     *
     * @param User  $user  User.
     * @param Email $email Email model.
     *
     * @return EmailResponse
     */
    public function sendEidasEmail(User $user, Email $email): EmailResponse
    {
        $this->refreshOtpHash($user);

        $response = $this->httpClient->post(
            $this->getUserConfiguration($user),
            AccessPoints::POST_EMAIL,
            $this->getEmailData($user, $email, true)
        );

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Upload a document.
     *
     * @param User       $user       User owner.
     * @param Attachment $attachment Attachment.
     *
     * @return AttachmentUploadedResponse.
     */
    public function uploadAttachment(User $user, Attachment $attachment): AttachmentUploadedResponse
    {
        $response = $this->httpClient->post(
            $this->getUserConfiguration($user),
            AccessPoints::POST_ATTACHMENT,
            [],
            $attachment
        );

        return new AttachmentUploadedResponse($response['status'], $response['result']);
    }

    /**
     * Get informations about a simple registered email.
     *
     * @param User   $user User.
     * @param string $id   Identifier.
     *
     * @return EmailResponse
     */
    public function getEmailInformations(User $user, string $id): EmailResponse
    {
        $response = $this->httpClient->get(
            $this->getUserConfiguration($user),
            AccessPoints::GET_EMAIL_INFORMATIONS,
            ['id' => $id]
        );

        return new EmailResponse($response['status'], $response['result']);
    }

    /**
     * Get registered mail list by ids.
     *
     * @param User $user
     * @param array $ids
     *
     * @return array
     */
    public function getRegisteredMailList(User $user, array $ids = []): array
    {
        $response = $this->httpClient->get(
            $this->getUserConfiguration($user),
            AccessPoints::GET_REGISTERED_MAIL_LIST,
            ['mail' => $ids]
        );

        return array_map(function ($email) use ($response) {
            return new EmailResponse($response['status'], $email);
        }, $response['result']);
    }
}
