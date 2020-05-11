<?php

use Kptask\Core\Adapter\Email\EmailServiceAdapter;
use Kptask\Core\Adapter\Email\EmailValidatorServiceInterface;
use Kptask\Core\Notification\Factory\NotificationFactory;
use Kptask\Core\Notification\NotificationManager;
use Kptask\Core\Notification\NotificationManagerInterface;
use Kptask\User\Factory\UserEntityFactory;
use Kptask\User\Validator\UserValidationManager;
use Zend\Session\SessionManager;
use Zend\Session\ManagerInterface;
use Zend\Session\Config\SessionConfig;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface as Logger;
use Zend\Config\Config;
use Tamtamchik\SimpleFlash\Flash;
use Bipsys\Admin\Acl\Acl;
/* import users */
use Kptask\User\Repository\UserRepository;
use Kptask\Core\Repository\UserRepositoryInterface;
use Kptask\User\Mapper\UserDbAdapter;
use Kptask\Core\Mapper\UserMapperInterface;
use Kptask\Core\Validator\ValidatorInterface;
use Kptask\User\Validator\UserValidator;
use Kptask\User\Service\UserService;
use Kptask\Core\Service\UserServiceInterface;
//mailer
use Kptask\Core\Email\Mailer;
use Kptask\Core\Email\MailerInterface;
use Zend\Validator\EmailAddress;

$containerBuilder = new \DI\ContainerBuilder;
/* @var \DI\Container $container */
$container = $containerBuilder->build();

$container->set(\FastRoute\Dispatcher::class, function () {
    $routeList = require APP_PATH.'/config/routes.php';

    /** @var \FastRoute\Dispatcher $dispatcher */
    return FastRoute\simpleDispatcher(
        function (\FastRoute\RouteCollector $r) use ($routeList) {
            foreach ($routeList as $routeDef) {
                $r->addRoute($routeDef[0], $routeDef[1], $routeDef[2]);
            }
        }
    );
});

$container->set(Kptask\Core\Middleware\AuthMiddleware::class, function () use ($container) {
    $aclData = require APP_PATH . '/config/acl.php';

    return new \Kptask\Core\Middleware\AuthMiddleware(
        $container->get(ManagerInterface::class),
        $container->get(Flash::class),
        $aclData
    );
});


$container->set(Config::class, function () {
    $params = include(APP_PATH . "/config/config.php");
    $config = new \Zend\Config\Config($params);
    $config = $config->merge(new \Zend\Config\Config(include(APP_PATH . "/config/config-local.php")));

    return $config;
});

$container->set(ManagerInterface::class, function () {
    $sessionConfig = new SessionConfig();
    $sessionConfig->setOptions([
        'remember_me_seconds' => 2592000, //2592000, // 30 * 24 * 60 * 60 = 30 days
        'use_cookies'         => true,
        'cookie_httponly'     => true,
        'name'                => 'kptask',
        'cookie_lifetime'     => 60 * 60 * 2,
    ]);

    $session = new SessionManager($sessionConfig);
    $session->start();

    return $session;
});

$container->set(Flash::class, function () use ($container) {
    //session needs to be started for flash
    $container->get(ManagerInterface::class);
    $flash = new Flash();

    return $flash;
});


$container->set(Logger::class, function () {
    $logger = new \Monolog\Logger('skeletonlog');

    $date = new \DateTime('now', new \DateTimeZone('Europe/Belgrade'));
    $logDir = APP_PATH . '/data/logs/' . $date->format('Y') . '-' . $date->format('m');
    $logFile = $logDir . '/' . gethostname() . '-' . $date->format('d') . '.log';
    $debugLog = APP_PATH . '/data/logs/debug.log';
    // create dir or file if needed
    if (!is_dir($logDir)) {
        mkdir($logDir);
    }
    if (!file_exists($logFile)) {
        touch($logFile);
    }

    $logger->pushHandler(
        new StreamHandler($logFile)
    );
    $logger->pushHandler(
        new StreamHandler(
            $debugLog,
            \Monolog\Logger::DEBUG
        )
    );

    ErrorHandler::register($logger);

    return $logger;
});

$container->set(PDO::class, function () use ($container) {
    $config = $container->get(Config::class);
    $dsn = "mysql:host={$config->db->host};dbname={$config->db->name}";
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );
    return new \PDO($dsn, $config->db->user, $config->db->pass, $options);
});

$container->set(DateTime::class, function () use ($container) {
    return new \DateTime('now', new DateTimeZone('Europe/Belgrade'));
});


$container->set(\DateTime::class, function () use ($container) {
    $dt = new \DateTime('now', new \DateTimeZone($container->get(Config::class)->offsetGet('timezone')));
    return $dt;
});


$container->set(\Twig\Environment::class, function () use ($container) {
    $loader = new \Twig\Loader\FilesystemLoader(
        __DIR__ . '/../themes/default/'
    );

    $te = new \Twig\Environment($loader, array(
        'debug' => true,
    ));
    $te->addExtension(new \Twig\Extension\DebugExtension());
    // $te->addExtension(new \Twig\Extensions\Extension_I18n());
    return $te;
});

$container->set(EmailAddress::class, function () use ($container) {
    return new EmailAddress();
});
$container->set(EmailValidatorServiceInterface::class, function () use ($container) {
    return new EmailServiceAdapter($container->get(EmailAddress::class));
});

/**
 * Register users to container and wrap it behind interface.
 */

$container->set(UserEntityFactory::class, function () use ($container) {
    return new UserEntityFactory();
});

$container->set(\Zend\Mail\Message::class, function () use ($container) {
    return new \Zend\Mail\Message();
});

$container->set(\Zend\Mail\Transport\Sendmail::class, function () use ($container) {
    return new \Zend\Mail\Transport\Sendmail();
});

$container->set(UserMapperInterface::class, function () use ($container) {
    return new UserDbAdapter($container->get(PDO::class));
});

$container->set(UserRepositoryInterface::class, function () use ($container) {
    return new UserRepository($container->get(UserMapperInterface::class), $container->get(DateTime::class));
});

$container->set(ValidatorInterface::class, function () use ($container) {
    return new UserValidator($container->get(Flash::class), $container->get(UserRepositoryInterface::class));
});

$container->set(UserValidationManager::class, function () use ($container) {
    $valConfig = require APP_PATH . '/config/validate.php';
    return new UserValidationManager($container->get(UserRepositoryInterface::class), $valConfig, $container->get(EmailValidatorServiceInterface::class));
});

$container->set(UserServiceInterface::class, function () use ($container) {
    return new UserService($container->get(UserRepositoryInterface::class), $container->get(ManagerInterface::class), $container->get(UserEntityFactory::class), $container->get(UserValidationManager::class), $container->get(NotificationManagerInterface::class));
});

/**
 * Here we create access to to container instance - This can only be used in factory classes
 */
$container->set(NotificationFactory::class, function () use ($container) {
    return new NotificationFactory($container->get(\Psr\Container\ContainerInterface::class));
});

$container->set(NotificationManagerInterface::class, function () use ($container) {
    $notifConfig = require APP_PATH . '/config/notification.php';
    return new NotificationManager($notifConfig, $container->get(NotificationFactory::class));
});


return $container;
