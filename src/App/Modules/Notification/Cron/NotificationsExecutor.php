<?php

namespace App\Modules\Notification\Cron;

use App\Modules\Notification\Model\Entity\NotificationEntity;
use DateTime;
use Framework\Cron\AbstractExecutor;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class NotificationsExecutor
 * @package App\Modules\Notifcation\Cron
 */
class NotificationsExecutor extends AbstractExecutor
{
    /** @var \App\Modules\Notification\Model\Repository\NotificationRepository */
    protected $notificationRepository;

    /** @var \Anddye\Mailer\Mailer */
    protected $mailer;

    /**
     * NotificationsExecutor constructor.
     * @param array|null $args
     */
    public function __construct(?array $args = null)
    {
        parent::__construct($args);
        $this->notificationRepository = $this->app->getContainer()->get('notificationRepository');
        $this->mailer = $this->app->getContainer()->get('mailer');
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $usersToNotify = $this->notificationRepository->getNotifiableUsers();

        foreach ($usersToNotify as $userToNotify) {
            $notification = new NotificationEntity(
                [
                    'userId' => $userToNotify['userId'],
                    'petId' => $userToNotify['petId'],
                    'careId' => $userToNotify['careId']
                ]
            );

            $sent = $this->sendMail($userToNotify) ? 1 : 0;
            $notification->setSent($sent);

            $this->notificationRepository->save($notification);
        }

        $this->output->writeln('Finished');
    }

    /**
     * @param array $userToNotify
     * @return int
     */
    protected function sendMail(array $userToNotify): int
    {
        $view = 'email/user-appointment.html';
        $subject = 'PetCare appointment notification';

        $date = new DateTime($userToNotify['appointmentDate']);
        $userToNotify['appointmentDate'] = $date->format('d/m/Y');
        $userToNotify['appointmentTime'] = $date->format('H:i:s');

        return $this->mailer->sendMessage(
            $view,
            $userToNotify,
            function ($message) use ($userToNotify, $subject) {
                $message->setTo($userToNotify['userEmail'], $userToNotify['userFirstname']);
                $message->setSubject($subject);
            }
        );
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Notifies all users when they soon have an appointment';
    }
}
