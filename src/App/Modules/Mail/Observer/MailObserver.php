<?php

namespace App\Modules\Mail\Observer;

use Anddye\Mailer\Mailer;
use App\Modules\Activation\Model\Repository\ActivationRepository;
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

    /** @var ActivationRepository */
    protected $activationRepository;

    /**
     * MailObserver constructor.
     * @param Mailer $mailer
     * @param ActivationRepository $activationRepository
     * @param null $subject
     */
    public function __construct(
        Mailer $mailer,
        ActivationRepository $activationRepository,
        $subject = null
    ) {
        parent::__construct($subject);
        $this->mailer = $mailer;
        $this->activationRepository = $activationRepository;
    }

    /**
     * @param SubjectInterface $subject
     * @throws \Framework\Exception\RepositoryException
     * @throws \Exception
     */
    public function subscribe(SubjectInterface $subject)
    {
        $user = $subject->getCurrentUser();
        $id = $user->getId();

        $activation = $this->activationRepository->fetchOneBy('userId', $id);
        $activationCode = $activation->getActivationCode();

        $dotenv = new Dotenv();
        $dotenv->load(ENV_FILE);

        $websiteUrl = $_ENV['WEBSITE_URL'];

        $link = $websiteUrl . "/user/activate/" . $id . "/" . $activationCode;

        $sent = $this->mailer->sendMessage(
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

        if ($sent) {
            $activation->setMailSent(1);
            $this->activationRepository->save($activation);
        }
    }
}
