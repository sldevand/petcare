<?php

namespace App\Modules\Mail\Observer;

use App\Modules\Activation\Model\Repository\ActivationRepository;
use App\Modules\Mail\Service\MailSender;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Observer\SubjectInterface;
use Framework\Observer\Observer;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class UserSubscribeObserver
 * @package App\Modules\Mail\Observer
 */
class UserSubscribeObserver extends Observer
{
    /** @var MailSender */
    protected $mailSender;

    /** @var ActivationRepository */
    protected $activationRepository;

    /**
     * UserSubscribeObserver constructor.
     * @param MailSender $mailSender
     * @param ActivationRepository $activationRepository
     * @param null $subject
     */
    public function __construct(
        MailSender $mailSender,
        ActivationRepository $activationRepository,
        $subject = null
    ) {
        parent::__construct($subject);
        $this->mailSender = $mailSender;
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
        $activation = $this->activationRepository->fetchOneBy('userId', $user->getId());

        if ($this->sendMail($user, $activation->getActivationCode())) {
            $activation->setMailSent(true);
            $this->activationRepository->save($activation);
        }
    }

    /**
     * @param EntityInterface $user
     * @param string $activationCode
     * @return int
     */
    protected function sendMail(EntityInterface $user, string $activationCode): int
    {
        $dotenv = new Dotenv();
        $dotenv->load(ENV_FILE);

        $frontWebsiteUrl = $_ENV['FRONT_WEBSITE_URL'];
        $view = 'email/user-activation.html.twig';
        $link = $frontWebsiteUrl . "/account/activate/" . $user->getId() . "/" . $activationCode;
        $subject = 'PetCare subscription';

        return $this->mailSender->sendMailWithLink($view, $user, $link, $subject);
    }
}
