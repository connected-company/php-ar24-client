<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

use Connected\Ar24\Exception\Ar24ApiException;
use Connected\Ar24\Exception\Ar24ClientException;
use Connected\Ar24\Model\Attachment;
use Connected\Ar24\Model\Sender;
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
     * @var Sender
     */
    private $sender;

    /**
     * @var string|null
     */
    private $senderId;

    /**
     * @var string
     */
    private $webhook;

    /**
     * Constructor.
     *
     * @param Sender $sender  Sender.
     * @param string $baseUri Base URI.
     * @param string $webhook Webhook.
     * @param float  $timeout Timeout.
     */
    public function __construct(Sender $sender, string $baseUri, string $webhook, float $timeout)
    {
        $this->sender = $sender;
        $this->webhook = $webhook;
        $this->guzzle = new GuzzleClient(['base_uri' => $baseUri, 'timeout' => $timeout]);
    }

    /**
     * Get on access point.
     * Parameters are sent in the URL.
     *
     * @param string $accessPoint Access point.
     * @param array  $parameters  Parameters sent in the URI.
     *
     * @return array
     */
    public function get(string $accessPoint, array $parameters = []): array
    {
        $response = $this->guzzle->get(
            $accessPoint . '?' . http_build_query(
                array_merge(
                    $parameters,
                    ['token' => $this->sender->getToken(), 'id_user' => $this->getUserIdentifier()]
                )
            )
        );

        return $this->processResponse($response);
    }

    /**
     * Post on access point.
     * Parameters are sent with multipart.
     *
     * @param string          $accessPoint Access point.
     * @param array           $parameters  Parameters sent in multipart.
     * @param Attachment|null $attachment  Attachment to send.
     *
     * @return array
     */
    public function post(string $accessPoint, array $parameters = [], ?Attachment $attachment = null): array
    {
        $data['multipart'] = [
            ['name' => 'token', 'contents' => $this->sender->getToken()],
            ['name' => 'id_user', 'contents' => $this->getUserIdentifier()],
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

    /**
     * Get the user identifier, required for some access points.
     *
     * @throws Ar24ClientException Unable to retrieve the user identifier.
     *
     * @return string
     */
    private function getUserIdentifier(): string
    {
        if ($this->senderId) {
            return $this->senderId;
        }

        $response = $this->guzzle->get(
            AccessPoints::USER_GET_USER . '?' . http_build_query(
                ['token' => $this->sender->getToken(), 'email' => $this->sender->getEmail()]
            )
        );

        $response = $this->processResponse($response);
        if (isset($response['result']['id'])) {
            $this->senderId = $response['result']['id'];
        } else {
            throw new Ar24ClientException('Unable to retrieve the user identifier', 500);
        }

        return $this->senderId;
    }
}
