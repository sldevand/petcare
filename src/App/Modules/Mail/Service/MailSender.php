<?php

namespace App\Modules\Mail\Service;

use Anddye\Mailer\Mailer;

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
     * @param $view
     * @param $user
     * @param $link
     * @param $subject
     * @return int
     */
    public function sendMailWithLink($view, $user, $link, $subject)
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
