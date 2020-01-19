<?php

use App\Modules\Activation\Model\Repository\ActivationRepository;
use App\Modules\Care\Model\Repository\CareRepository;
use App\Modules\Mail\Observer\MailObserver;
use App\Modules\Pet\Controller\PetController;
use App\Modules\Pet\Model\Repository\PetCareRepository;
use App\Modules\Pet\Model\Repository\PetImageRepository;
use App\Modules\Pet\Model\Repository\PetRepository;
use App\Modules\Token\Helper\Token;
use App\Modules\User\Controller\UserApiController;
use App\Modules\User\Controller\UserController;
use App\Modules\User\Model\Repository\UserPetRepository;
use App\Modules\User\Model\Repository\UserRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;

require_once FRAMEWORK_DIR . '/dependencies.php';

$dotenv = new Dotenv();
$dotenv->load(ENV_FILE);

$container['mailer'] = function ($container) {
    $twig = $container['view'];
    $mailer = new \Anddye\Mailer\Mailer($twig, [
        'host' => $_ENV['SMTP_HOST'],  // SMTP Host
        'port' => $_ENV['SMTP_PORT'],  // SMTP Port
        'username' => $_ENV['SMTP_USERNAME'],  // SMTP Username
        'password' => $_ENV['SMTP_PASSWORD'],  // SMTP Password
        'protocol' => $_ENV['SMTP_PROTOCOL']   // SSL or TLS
    ]);

    // Set the details of the default sender
    $mailer->setDefaultFrom('no-reply@mail.com', 'Webmaster');

    return $mailer;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(VIEWS_DIR);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

//helpers
$container['tokenHelper'] = function (ContainerInterface $c) {
    return new Token();
};

// repositories
$container['careRepository'] = function (ContainerInterface $c) {
    return new CareRepository($c->get('pdo'), $c->get('defaultValidator'));
};

$container['petImageRepository'] = function (ContainerInterface $c) {
    return new PetImageRepository($c->get('pdo'), $c->get('defaultValidator'));
};

$container['petCareRepository'] = function (ContainerInterface $c) {
    return new PetCareRepository($c->get('pdo'), $c->get('defaultValidator'));
};

$container['petRepository'] = function (ContainerInterface $c) {
    return new PetRepository(
        $c->get('pdo'),
        $c->get('defaultValidator'),
        $c->get('petImageRepository'),
        $c->get('petCareRepository')
    );
};

$container['userPetRepository'] = function (ContainerInterface $c) {
    return new UserPetRepository($c->get('pdo'), $c->get('defaultValidator'));
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
        $c->get('userPetRepository'),
        $c->get('petRepository')
    );
};

// observers
$container['mailObserver'] = function (ContainerInterface $c) {
    return new MailObserver($c->get('mailer'), $c->get('activationRepository'));
};

// controllers
$container['petController'] = function (ContainerInterface $c) {
    return new PetController($c->get('petRepository'), $c->get('userRepository'));
};

$container['userController'] = function (ContainerInterface $c) use ($settings) {
    $userController = new UserController(
        $c->get('userRepository'),
        $c->get('tokenHelper'),
        $c->get('activationRepository'),
        $c->get('logger'),
        $settings
    );
    $userController->attach($c->get('mailObserver'));

    return $userController;
};

$container['userApiController'] = function (ContainerInterface $c) use ($settings) {
    $userApiController = new UserApiController($c->get('userRepository'), $c->get('userRepository'));

    return $userApiController;
};
