<?php declare(strict_types=1);

namespace Connected\Ar24\Exception;

/**
 * Exception for Ar24 API.
 */
class Ar24ApiException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string      $message Message.
     * @param string|null $slug    Error slug.
     * @param integer     $code    Error code.
     */
    public function __construct(string $message, ?string $slug = null, int $code)
    {
        if ($slug) {
            $message = $slug ? $message . ' (' . $slug . ')' : $message;
        }

        parent::__construct('AR24 API : ' . $message, $code);
    }
}
