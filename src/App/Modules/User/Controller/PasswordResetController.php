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

    /**
     * PasswordResetController constructor.
     * @param RepositoryInterface $repository
     * @param PasswordResetRepository $passwordResetRepository
     * @param MailSender $mailSender
     */
    public function __construct(
        RepositoryInterface $repository,
        PasswordResetRepository $passwordResetRepository,
        MailSender $mailSender
    ) {
        parent::__construct($repository);
        $this->passwordResetRepository = $passwordResetRepository;
        $this->mailSender = $mailSender;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function execute(Request $request, Response $response, $args = []): Response
    {
        $contents = $request->getBody()->getContents();
        $params = json_decode($contents, true);

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
            if ($diff > self::MAX_TIME_ELAPSED) {
                $passwordResetEntity->setMailSent(false);
                $this->passwordResetRepository->save($passwordResetEntity);
            }

            if ($passwordResetEntity->getMailSent()) {
                $remaining = self::MAX_TIME_ELAPSED - $diff;
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
        } catch (RepositoryException $e) {
            $argEmail = $params['email'];
            return $this->sendError($response, "This user with email $argEmail doesn't exists !");
        } catch (Exception $e) {
            return $this->sendError($response, "An error occurred on password reset !");
        }
    }

    /**
     * @param EntityInterface $user
     * @param string $resetCode
     * @return int
     */
    protected function sendMail(EntityInterface $user, string $resetCode): int
    {
        $dotenv = new Dotenv();
        $dotenv->load(ENV_FILE);
        $frontWebsiteUrl = $_ENV['FRONT_WEBSITE_URL'];

        $view = 'email/password-reset.html.twig';
        $link = $frontWebsiteUrl . "/user/passwordReset/" . $user->getId() . "/" . $resetCode;
        $subject = 'PetCare password reset';

        return $this->mailSender->sendMailWithLink($view, $user, $link, $subject);
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
