<?php

namespace App\Modules\Notification\Cron;

use App\Modules\Notification\Model\Entity\NotificationEntity;
use DateInterval;
use DateTime;
use DateTimeZone;
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

    /** @var \App\Modules\Mail\Service\MailSender */
    protected $mailSender;


    /**
     * NotificationsExecutor constructor.
     * @param array|null $args
     */
    public function __construct(?array $args = null)
    {
        parent::__construct($args);
        $this->notificationRepository = $this->app->getContainer()->get('notificationRepository');
        $this->mailSender = $this->app->getContainer()->get('mailSender');
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
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function sendMail(array $userToNotify): int
    {
        $view = VIEWS_DIR . '/email/user-appointment.html';
        $body = file_get_contents($view);

        $subject = 'PetCare appointment notification';

        $date = new DateTime($userToNotify['appointmentDate']);
        $date->add(new DateInterval('PT2H'));
        $userToNotify['appointmentDate'] = $date->format('d/m/Y');
        $userToNotify['appointmentTime'] = $date->format('H:i:s');

        return $this->mailSender->sendMail($body, $userToNotify, $userToNotify['userEmail'], $subject);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Notifies all users when they soon have an appointment';
    }
}
