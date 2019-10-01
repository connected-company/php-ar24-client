<?php declare(strict_types=1);

namespace Connected\Ar24\Exception;

/**
 * Exception for Ar24 client.
 */
class Ar24ClientException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string  $message Message.
     * @param integer $code    Error code.
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct('AR24 Client : ' . $message, $code);
    }
}
