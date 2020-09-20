<?php

use App\Modules\Activation\Model\Repository\ActivationRepository;
use App\Modules\Care\Model\Repository\CareRepository;
use App\Modules\Image\Service\ImageManager;
use App\Modules\Mail\Observer\UserSubscribeObserver;
use App\Modules\Mail\Service\MailSender;
use App\Modules\Notification\Model\Repository\NotificationRepository;
use App\Modules\PasswordReset\Model\Repository\PasswordResetRepository;
use App\Modules\Pet\Controller\PetController;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\Token\Helper\Token;
use App\Modules\User\Controller\ActivateController;
use App\Modules\User\Controller\LoginController;
use App\Modules\User\Controller\PasswordChangeController;
use App\Modules\User\Controller\PasswordResetController;
use App\Modules\User\Controller\SubscribeController;
use App\Modules\User\Controller\UserApiController;
use App\Modules\Care\Controller\CareController;
use App\Modules\User\Helper\ApiKey;
use App\Modules\User\Model\Repository\UserRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;

require_once FRAMEWORK_DIR . '/dependencies.php';

$dotenv = new Dotenv();
$dotenv->load(ENV_FILE);

//core dependencies
$container['mailer'] = function ($container) {
    $twig = $container['view'];
    $mailer = new \Anddye\Mailer\Mailer($twig, [
        'host' => $_ENV['SMTP_HOST'],  // SMTP Host
        'port' => $_ENV['SMTP_PORT'],  // SMTP Port
        'username' => $_ENV['SMTP_USERNAME'],  // SMTP Username
        'password' => $_ENV['SMTP_PASSWORD'],  // SMTP Password
        'protocol' => $_ENV['SMTP_PROTOCOL']   // SSL or TLS
    ]);

    $mailer->setDefaultFrom('no-reply@mail.com', 'Petcare Team');

    return $mailer;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(VIEWS_DIR);
    $basePath = rtrim(
        str_ireplace(
            'index.php',
            '',
            $container['request']->getUri()->getBasePath()
        ),
        '/'
    );
    $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

//services
$container['mailSender'] = function (ContainerInterface $c) {
    return new MailSender($c->get('mailer'));
};

$container['imageManager'] = function (ContainerInterface $c) use ($settings) {
    return new ImageManager($c->get('fileManager'), $settings);
};

//helpers
$container['tokenHelper'] = function (ContainerInterface $c) {
    return new Token();
};

$container['apiKeyHelper'] = function (ContainerInterface $c) {
    return new ApiKey($c->get('userRepository'));
};

// repositories
$container['careRepository'] = function (ContainerInterface $c) {
    return new CareRepository($c->get('pdo'), $c->get('defaultValidator'));
};

$container['passwordResetRepository'] = function (ContainerInterface $c) {
    return new PasswordResetRepository($c->get('pdo'), $c->get('defaultValidator'));
};

$container['petImageRepository'] = function (ContainerInterface $c) {
    return new PetImageRepository($c->get('pdo'), $c->get('defaultValidator'));
};


$container['petRepository'] = function (ContainerInterface $c) {
    return new PetRepository(
        $c->get('pdo'),
        $c->get('defaultValidator'),
        $c->get('petImageRepository'),
        $c->get('careRepository'),
        $c->get('imageManager')
    );
};

$container['activationRepository'] = function (ContainerInterface $c) {
    return new ActivationRepository(
        $c->get('pdo'),
        $c->get('defaultValidator')
    );
};

$container['userRepository'] = function (ContainerInterface $c) {
    return new UserRepository(
        $c->get('pdo'),
        $c->get('defaultValidator'),
        $c->get('petRepository'),
        $c->get('activationRepository')
    );
};

$container['notificationRepository'] = function (ContainerInterface $c) {
    return new NotificationRepository(
        $c->get('pdo'),
        $c->get('defaultValidator'),
        $c->get('careRepository'),
        $c->get('petRepository'),
        $c->get('userRepository')
    );
};

// observers
$container['mailObserver'] = function (ContainerInterface $c) {
    return new UserSubscribeObserver($c->get('mailSender'), $c->get('activationRepository'));
};

// controllers
$container['petController'] = function (ContainerInterface $c) {
    return new PetController(
        $c->get('petRepository'),
        $c->get('userRepository'),
        $c->get('apiKeyHelper'),
        $c->get('logger'),
        $c->get('imageManager')
    );
};

$container['careController'] = function (ContainerInterface $c) {
    return new CareController(
        $c->get('careRepository'),
        $c->get('userRepository'),
        $c->get('apiKeyHelper'),
        $c->get('logger')
    );
};

$container['userApiController'] = function (ContainerInterface $c) {
    return new UserApiController($c->get('userRepository'), $c->get('userRepository'), $c->get('apiKeyHelper'));
};

$container['userLoginController'] = function (ContainerInterface $c) {
    return new LoginController($c->get('userRepository'), $c->get('activationRepository'), $c->get('logger'));
};

$container['userActivateController'] = function (ContainerInterface $c) {
    return new ActivateController($c->get('userRepository'), $c->get('activationRepository'), $c->get('logger'));
};

$container['userPasswordResetController'] = function (ContainerInterface $c) {
    return new PasswordResetController(
        $c->get('userRepository'),
        $c->get('passwordResetRepository'),
        $c->get('mailSender'),
        $c->get('logger')
    );
};

$container['userPasswordChangeController'] = function (ContainerInterface $c) use ($settings) {
    return new PasswordChangeController(
        $c->get('userRepository'),
        $c->get('passwordResetRepository'),
        $c->get('logger'),
        $c->get('tokenHelper'),
        $settings
    );
};

$container['userSubscribeController'] = function (ContainerInterface $c) use ($settings) {
    $userSubscribeController = new SubscribeController(
        $c->get('userRepository'),
        $c->get('tokenHelper'),
        $c->get('activationRepository'),
        $c->get('logger'),
        $settings
    );
    $userSubscribeController->attach($c->get('mailObserver'));

    return $userSubscribeController;
};
