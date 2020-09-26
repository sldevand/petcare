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
        $vars = [
            'firstName' => $user->getFirstName(),
            'link' => $link
        ];

        return $this->sendMail($view, $vars, $user->getEmail(), $subject);
    }

    /**
     * @param string $view
     * @param array $vars
     * @param string $to
     * @param string $subject
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail(string $view, array $vars, string $to, string $subject)
    {
        $this->mailer->Subject = $subject;

        $body = TemplateProcessor::process($view, $vars);

        $this->mailer->MsgHTML($body);
        $this->mailer->AddAddress($to);

        return $this->mailer->send();
    }

    /**
     * @param string $view
     * @param EntityInterface $user
     * @param string $link
     * @param string $subject
     * @return int
     */
    public function notifyUsersForApproachingAppointment(
        string $view,
        EntityInterface $user,
        string $link,
        string $subject
    ): int {
        return $this->mailer->sendMessage(
            $view,
            [
                'firstName' => $user->getFirstName(),
                'link' => $link
            ],
            function ($message) use ($user, $subject) {
                $message->setTo($user->getEmail(), $user->getFirstName());
                $message->setSubject($subject);
            }
        );
    }
}
