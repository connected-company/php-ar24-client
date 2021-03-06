<?php declare(strict_types=1);

namespace Connected\Ar24\Component;

/**
 * List access points.
 */
class AccessPoints
{
    // Get user informations.
    const USER_GET_USER = 'user/';

    // Send an email to a recipient.
    const POST_EMAIL = 'mail/';

    // Get informations about an email.
    const GET_EMAIL_INFORMATIONS = 'mail/';

    // Post an attachment.
    const POST_ATTACHMENT = 'attachment/';

    // Authenticate with OTP.
    const AUTHENTICATE_OTP = 'user/auth_otp/';

    // Get registered mail list.
    const GET_REGISTERED_MAIL_LIST = 'user/mail';
}
