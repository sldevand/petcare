<?php

namespace App\Modules\User\Controller;

use App\Modules\Mail\Service\MailSender;
use App\Modules\PasswordReset\Model\Entity\PasswordResetEntity;
use App\Modules\PasswordReset\Model\Repository\PasswordResetRepository;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Framework\Exception\RepositoryException;
use Framework\Helper\DateHelper;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class PasswordResetController
 * @package App\Modules\User\Controller
 */
class PasswordResetController extends AbstractController
{
    const MAX_TIME_ELAPSED = 10;

    /** @var MailSender */
    protected $mailSender;

    /** @var PasswordResetRepository */
    protected $passwordResetRepository;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * PasswordResetController constructor.
     * @param RepositoryInterface $repository
     * @param PasswordResetRepository $passwordResetRepository
     * @param MailSender $mailSender
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        PasswordResetRepository $passwordResetRepository,
        MailSender $mailSender,
        LoggerInterface $logger
    ) {
        parent::__construct($repository);
        $this->passwordResetRepository = $passwordResetRepository;
        $this->mailSender = $mailSender;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function execute(Request $request, Response $response, $args = []): Response
    {
        $params = $this->getBodyJsonParams($request);

        if (empty($params['email'])) {
            return $this->sendError($response, "Email field is missing !", StatusCode::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->repository->fetchOneBy('email', $params['email']);

            $resetCode = bin2hex(random_bytes(24));
            $passwordResetEntity = $this->getPasswordEntity($user, $resetCode);

            if (empty($date = $passwordResetEntity->getUpdatedAt())) {
                $date = $passwordResetEntity->getCreatedAt();
            }

            $diff = DateHelper::timeElapsedInMinutes($date);
            $remaining = self::MAX_TIME_ELAPSED - $diff;
            if ($remaining <= 0) {
                $passwordResetEntity->setMailSent(false);
                $passwordResetEntity = $this->passwordResetRepository->save($passwordResetEntity);
            }

            if ($passwordResetEntity->getMailSent()) {
                return $this->sendSuccess($response, "Mail was already sent ! Please wait $remaining minutes");
            }

            if (empty($this->sendMail($user, $resetCode))) {
                return $this->sendError($response, "No mail was sent, please contact us !");
            }

            $passwordResetEntity->setMailSent(true);
            $this->passwordResetRepository->save($passwordResetEntity);

            return $this->sendSuccess(
                $response,
                "We sent you an email, please click the link in it to reset your password"
            );
        } catch (RepositoryException $exception) {
            $argEmail = $params['email'];
            return $this->sendError($response, "This user with email $argEmail doesn't exists !");
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            return $this->sendError($response, "An error occurred on password reset !");
        }
    }

    /**
     * @param EntityInterface $user
     * @param string $resetCode
     * @return int
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function sendMail(EntityInterface $user, string $resetCode): int
    {
        $dotenv = new Dotenv();
        $dotenv->load(ENV_FILE);
        $frontWebsiteUrl = $_ENV['FRONT_WEBSITE_URL'];

        $view = VIEWS_DIR . '/email/password-reset.html';
        $body = file_get_contents($view);

        $link = $frontWebsiteUrl . "/passwordChange/" . $user->getId() . "/" . $resetCode;
        $subject = 'PetCare password reset';

        return $this->mailSender->sendMailWithLink($body, $user, $link, $subject);
    }

    /**
     * @param EntityInterface $user
     * @param string $resetCode
     * @return EntityInterface
     * @throws Exception
     */
    protected function getPasswordEntity(EntityInterface $user, string $resetCode): EntityInterface
    {
        try {
            $passwordResetEntity = $this->passwordResetRepository->fetchOneBy('userId', $user->getId());
        } catch (RepositoryException $e) {
            $passwordResetEntity = new PasswordResetEntity(
                [
                    'userId' => $user->getId(),
                    'mailSent' => false,
                    'reset' => false
                ]
            );
        }

        $passwordResetEntity->setResetCode($resetCode);

        return $passwordResetEntity;
    }
}
