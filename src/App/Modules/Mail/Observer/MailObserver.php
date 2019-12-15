<?php

namespace App\Modules\Mail\Observer;

use Anddye\Mailer\Mailer;
use Framework\Api\Observer\SubjectInterface;
use Framework\Observer\Observer;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class MailObserver
 * @package App\Modules\Mail\Observer
 */
class MailObserver extends Observer
{
    /** @var Mailer */
    protected $mailer;

    /**
     * MailObserver constructor.
     * @param Mailer $mailer
     * @param null $subject
     */
    public function __construct(
        Mailer $mailer,
        $subject = null
    ) {
        parent::__construct($subject);
        $this->mailer = $mailer;
    }

    /**
     * @param SubjectInterface $subject
     */
    public function subscribe(SubjectInterface $subject)
    {
        $user = $subject->getCurrentUser();
        $id = $user->getId();
        $activationCode = $user->getActivationCode();

        $dotenv = new Dotenv();
        $dotenv->load(ENV_FILE);

        $websiteUrl = $_ENV['WEBSITE_URL'];

        $link = $websiteUrl . "/user/activate/" . $id . "/" . $activationCode;

        $this->mailer->sendMessage(
            'email/user-activation.html.twig',
            [
                'firstName' => $user->getFirstName(),
                'link' => $link
            ],
            function ($message) use ($user) {
                $message->setTo($user->getEmail(), $user->getFirstName());
                $message->setSubject('You have subscribed to PetCare!');
            }
        );
    }
}
