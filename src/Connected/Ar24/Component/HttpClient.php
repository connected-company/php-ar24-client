<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

use Connected\Ar24\Exception\Ar24ApiException;
use Connected\Ar24\Exception\Ar24ClientException;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\User;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * Guzzle wrapper.
 */
class HttpClient
{
    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * @var string
     */
    private $webhook;

    /**
     * Constructor.
     *
     * @param string $baseUri Base URI.
     * @param string $webhook Webhook.
     * @param float  $timeout Timeout.
     */
    public function __construct(string $baseUri, string $webhook, float $timeout)
    {
        $this->webhook = $webhook;
        $this->guzzle = new GuzzleClient(['base_uri' => $baseUri, 'timeout' => $timeout]);
    }

    /**
     * Authenticate the user on AR24 API.
     *
     * @param User $user User to authenticate.
     *
     * @return array
     */
    public function authenticate(User $user): array
    {
        $response = $this->guzzle->get(
            AccessPoints::USER_GET_USER . '?' . http_build_query(
                ['email' => $user->getEmail(), 'token' => $user->getToken()]
            )
        );

        return $this->processResponse($response);
    }

    /**
     * Get on access point.
     * Parameters are sent in the URL.
     *
     * @param UserConfiguration $userConfiguration User configuration.
     * @param string            $accessPoint       Access point.
     * @param array             $parameters        Parameters sent in the URI.
     *
     * @return array
     */
    public function get(UserConfiguration $userConfiguration, string $accessPoint, array $parameters = []): array
    {
        $response = $this->guzzle->get(
            $accessPoint . '?' . http_build_query(
                array_merge(
                    $parameters,
                    ['token' => $userConfiguration->getToken(), 'id_user' => $userConfiguration->getId()]
                )
            )
        );

        return $this->processResponse($response);
    }

    /**
     * Post on access point.
     * Parameters are sent with multipart.
     *
     * @param UserConfiguration $userConfiguration User configuration.
     * @param string            $accessPoint       Access point.
     * @param array             $parameters        Parameters sent in multipart.
     * @param Attachment|null   $attachment        Attachment to send.
     *
     * @return array
     */
    public function post(
        UserConfiguration $userConfiguration,
        string $accessPoint,
        array $parameters = [],
        ?Attachment $attachment = null
    ): array {
        $data['multipart'] = [
            ['name' => 'token', 'contents' => $userConfiguration->getToken()],
            ['name' => 'id_user', 'contents' => $userConfiguration->getId()],
            ['name' => 'webhook', 'contents' => $this->webhook],
        ];

        foreach ($parameters as $name => $contents) {
            $data['multipart'][] = ['name' => $name, 'contents' => $contents];
        }

        if ($attachment) {
            $data['multipart'][] = ['name' => 'file', 'contents' => fopen($attachment->getFilepath(), 'r')];
        }

        return $this->processResponse($this->guzzle->post($accessPoint, $data));
    }

    /**
     * Process guzzle response.
     *
     * @param GuzzleResponse $response Response.
     *
     * @throws Ar24ApiException Api is currently undergoing maintenance.
     * @throws Ar24ApiException Api returns an error.
     *
     * @return array
     */
    private function processResponse(GuzzleResponse $response): array
    {
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['status']) && strtolower($response['status']) === 'maintenance') {
            throw new Ar24ApiException('AR24 is currently undergoing maintenance.', null, 422);
        } elseif (isset($response['status']) && strtolower($response['status']) === 'error') {
            throw new Ar24ApiException($response['message'], $response['slug'], 422);
        }

        return $response;
    }
}
