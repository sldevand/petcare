<?php

namespace App\Modules\Mail\Service;

use Framework\Api\Entity\EntityInterface;
use Framework\Mail\TemplateProcessor;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class MailSender
 * @package App\Modules\Mail\Service
 */
class MailSender
{
    /** @var \PHPMailer\PHPMailer\PHPMailer */
    protected $mailer;

    /**
     * MailSender constructor.
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer
     */
    public function __construct(
        PHPMailer $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
     * @param string $view
     * @param \Framework\Api\Entity\EntityInterface $user
     * @param string $link
     * @param string $subject
     * @return int
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMailWithLink(string $view, EntityInterface $user, string $link, string $subject): int
    {
        $this->mailer->Subject = $subject;

        $body = TemplateProcessor::process(
            $view,
            [
                'firstName' => $user->getFirstName(),
                'link' => $link
            ]
        );

        $this->mailer->MsgHTML($body);
        $this->mailer->AddAddress($user->getEmail());

        return $this->mailer->send();
    }
}
