<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

use Connected\Ar24\Component\UserConfiguration;
use Connected\Ar24\Exception\Ar24ClientException;
use Connected\Ar24\Model\Email;
use Connected\Ar24\Model\User;
use OTPHP\TOTP;

/**
 * Client trait for AR24.
 */
trait ClientTrait
{
    /**
     * Prepare data for an email.
     *
     * @param User    $user  User.
     * @param Email   $email Email model.
     * @param boolean $eidas Send as an eIDAS email.
     *
     * @throws Ar24ClientException OTP code is empty.
     *
     * @return array
     */
    private function getEmailData(User $user, Email $email, bool $eidas = false): array
    {
        $data = $this->getGenericEmailData($email);

        foreach ($email->getAttachments() as $key => $attachment) {
            $data['attachment[' . $key . ']'] = $this->uploadAttachment($user, $attachment)->getId();
        }

        if ($eidas) {
            if (empty($user->getOtpCode())) {
                throw new Ar24ClientException('OTP code is empty', 500);
            }

            $data['eidas'] = true;
            $data['otp'] = TOTP::create($user->getOtpCode())->now();
        }

        return $data;
    }

    /**
     * Get generic email data.
     *
     * @param Email $email Email.
     *
     * @return array
     */
    private function getGenericEmailData(Email $email): array
    {
        return [
            'to_lastname' => $email->getRecipient()->getLastname(),
            'to_firstname' => $email->getRecipient()->getFirstname(),
            'to_company' => $email->getRecipient()->getCompany(),
            'to_email' => $email->getRecipient()->getEmail(),
            'dest_statut' => $email->getRecipient()->getStatus(),
            'ref_client' => $email->getRecipient()->getReference(),
            'content' => $email->getContent(),
            'ref_dossier' => $email->getReferenceDossier(),
            'ref_facturation' => $email->getReferenceFacturation()
        ];
    }

    /**
     * Get UserConfiguration from an User.
     *
     * @param User $user User.
     *
     * @return UserConfiguration
     */
    private function getUserConfiguration(User $user): UserConfiguration
    {
        return $this->configuration->getUserConfigurationByUser($user);
    }
}
