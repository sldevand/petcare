<?php

namespace App\Modules\User\Controller;

use Anddye\Mailer\Mailer;
use App\Modules\PasswordReset\Model\Entity\PasswordResetEntity;
use App\Modules\PasswordReset\Model\Repository\PasswordResetRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\AbstractController;
use Framework\Exception\RepositoryException;
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
    /** @var Mailer */
    protected $mailer;

    /** @var PasswordResetRepository */
    protected $passwordResetRepository;

    /**
     * PasswordResetController constructor.
     * @param RepositoryInterface $repository
     * @param Mailer $mailer
     * @param PasswordResetRepository $passwordResetRepository
     */
    public function __construct(
        RepositoryInterface $repository,
        Mailer $mailer,
        PasswordResetRepository $passwordResetRepository
    ) {
        parent::__construct($repository);
        $this->mailer = $mailer;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function execute(Request $request, Response $response, $args = []): Response
    {
        if (empty($args['email'])) {
            return $this->sendError(
                $response,
                "Email field is missing !",
                StatusCode::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $this->repository->fetchOneBy('email', $args['email']);

            $resetCode = bin2hex(random_bytes(24));

            $passwordResetEntity = new PasswordResetEntity(
                [
                    'userId'         => $user->getId(),
                    'reset'      => false,
                    'mailSent'       => false,
                    'resetCode' => $resetCode
                ]
            );

            $this->passwordResetRepository->save($passwordResetEntity);

            $dotenv = new Dotenv();
            $dotenv->load(ENV_FILE);
            $frontWebsiteUrl = $_ENV['FRONT_WEBSITE_URL'];

            $link = $frontWebsiteUrl . "/user/passwordReset/" . $user->getId() . "/" . $resetCode;

            $sent = $this->mailer->sendMessage(
                'email/password-reset.html.twig',
                [
                    'firstName' => $user->getFirstName(),
                    'link' => $link
                ],
                function ($message) use ($user) {
                    $message->setTo($user->getEmail(), $user->getFirstName());
                    $message->setSubject('PetCare password reset');
                }
            );

            if (!$sent) {
                return $this->sendError($response, "No mail was sent, please contact us !");
            }

            return $this->sendSuccess(
                $response,
                "We sent you an email, please click the link in it to reset your password"
            );
        } catch (RepositoryException $e) {
            $argEmail = $args['email'];
            return $this->sendError($response, "This user with email $argEmail doesn't exists !");
        } catch (Exception $e) {
            return $this->sendError($response, "An error occurred on password reset !");
        }
    }
}
