<?php declare(strict_types=1);

namespace Connected\Ar24\Model;

use Connected\Ar24\Exception\Ar24ClientException;

/**
 * Attachment model.
 */
class Attachment
{
    /**
     * @var string
     */
    private $filepath;

    /**
     * @var string|null
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $filepath File path.
     *
     * @throws Ar24ClientException File does not exists.
     */
    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new Ar24ClientException('File `' . $filepath . '` does not exists', 500);
        }

        $this->filepath = $filepath;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id API identifier.
     *
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
