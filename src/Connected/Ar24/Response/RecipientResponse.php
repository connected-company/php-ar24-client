<?php declare(strict_types=1);

namespace Connected\Ar24\Response;

use Connected\Ar24\Model\Recipient;

/**
 * Recipient model.
 */
class RecipientResponse extends Recipient
{
    /**
     * Constructor.
     *
     * @param array $data Response data.
     */
    public function __construct(array $data)
    {
        parent::__construct(
            $data['to_firstname'],
            $data['to_lastname'],
            $data['to_email'],
            $data['to_company'],
            $data['ref_client']
        );
    }
}
