<?php

namespace Framework\Mail;

use Anddye\Mailer\Mailer;
use Slim\Views\Twig;

/**
 * Class Mailer
 * @package Framework\Mail
 */
class MailerFactory
{
    /**
     * @param \Slim\Views\Twig $twig
     * @return \Anddye\Mailer\Mailer
     */
    public static function create(Twig $twig)
    {
        $mailer = new Mailer($twig, [
            'host' => $_ENV['SMTP_HOST'],
            'port' => $_ENV['SMTP_PORT'],
            'username' => $_ENV['SMTP_USERNAME'],
            'password' => $_ENV['SMTP_PASSWORD'],
            'protocol' => $_ENV['SMTP_PROTOCOL']
        ]);

        return $mailer;
    }
}
