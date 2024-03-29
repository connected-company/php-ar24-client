<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

use Connected\Ar24\Exception\Ar24ClientException;
use Connected\Ar24\Model\User;

/**
 * Configuration for AR24.
 */
class Configuration
{
    /**
     * Default timeout for http client (seconds).
     */
    const TIMEOUT = 20.0;

    /**
     * Demo environment.
     */
    const ENV_DEMO = 'demo';

    /**
     * Production environment.
     */
    const ENV_PROD = 'prod';

    /**
     * Base URI for demo environment.
     */
    const DEMO_API_URI = 'https://sandbox.ar24.fr/api/';

    /**
     * Base URI for production environment.
     */
    const PROD_API_URI = 'https://app.ar24.fr/api/';

    /**
     * @var array
     */
    private $userConfigurations;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $webhook;

    /**
     * @var float
     */
    private $timeout;

    /**
     * Constructor.
     *
     * @param string $environment Environment.
     * @param string $webhook     Webhook.
     * @param float  $timeout     Timeout.
     */
    public function __construct(string $environment, string $webhook, float $timeout = self::TIMEOUT)
    {
        $this->webhook = $webhook;
        $this->setEnvironment($environment)->setTimeout($timeout);
    }

    /**
     * Returns the environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Add a new UserConfiguration.
     *
     * @param UserConfiguration $userConfiguration User configuration.
     *
     * @return self
     */
    public function addUserConfiguration(UserConfiguration $userConfiguration): self
    {
        $this->userConfigurations[$userConfiguration->getEmail()] = $userConfiguration;

        return $this;
    }

    /**
     * Get UserConfiguration from User.
     *
     * @param User $user User.
     *
     * @return UserConfiguration
     */
    public function getUserConfigurationByUser(User $user): UserConfiguration
    {
        return $this->userConfigurations[$user->getEmail()];
    }

    /**
     * Returns the webhook.
     *
     * @return string
     */
    public function getWebhook(): string
    {
        return $this->webhook;
    }

    /**
     * Returns the timeout.
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Get the base URI based on the environment.
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->environment === self::ENV_DEMO ? self::DEMO_API_URI : self::PROD_API_URI;
    }

    /**
     * Set and validate the environment.
     *
     * @param string $environment Environment name.
     *
     * @throws Ar24ClientException Invalid environment.
     *
     * @return self
     */
    private function setEnvironment(string $environment): self
    {
        if (!in_array(strtolower($environment), [self::ENV_DEMO, self::ENV_PROD])) {
            throw new Ar24ClientException('The environment is invalid', 500);
        }

        $this->environment = $environment;

        return $this;
    }

    /**
     * Set and validate the timeout.
     *
     * @param float $timeout Timeout.
     *
     * @throws Ar24ClientException Invalid timeout.
     *
     * @return self
     */
    private function setTimeout(float $timeout): self
    {
        if ($timeout <= 0) {
            throw new Ar24ClientException('The timeout must be superior to 0', 500);
        }

        $this->timeout = $timeout;

        return $this;
    }
}
