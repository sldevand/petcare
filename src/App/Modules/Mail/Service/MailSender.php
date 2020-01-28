<?php

namespace App\Modules\Mail\Service;

use Anddye\Mailer\Mailer;
use Framework\Api\Entity\EntityInterface;

/**
 * Class MailSender
 * @package App\Modules\Mail\Service
 */
class MailSender
{
    /** @var Mailer */
    protected $mailer;

    /**
     * MailSender constructor.
     * @param Mailer $mailer
     */
    public function __construct(
        Mailer $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
     * @param string $view
     * @param EntityInterface $user
     * @param string $link
     * @param string $subject
     * @return int
     */
    public function sendMailWithLink(string $view, EntityInterface $user, string $link, string $subject): int
    {
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
