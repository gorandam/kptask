<?php
declare(strict_types=1);

namespace Kptask\Core\Middleware;

use Kptask\User\Entity\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Session\ManagerInterface;
use Tamtamchik\SimpleFlash\Flash;

/**
 * Class AuthMiddleware
 * @package Kptask\Core\Middleware
 */
class AuthMiddleware
{

    /**
     * @var ManagerInterface
     */
    private $sessionManager;

    /**
     * @var array
     */
    private $aclData;

    /**
     * @var Flash
     */
    private $flash;

    const GUEST_LEVEL = 0;

    /**
     * AuthMiddleware constructor.
     *
     * @param ManagerInterface $sessionManager $sessionManager
     * @param Flash $flash
     * @param array $aclData
     */
    public function __construct(ManagerInterface $sessionManager, Flash $flash, array $aclData)
    {
        $this->sessionManager = $sessionManager;
        $this->aclData = $aclData;
        $this->flash = $flash;
    }

    /**
     * Checks if user can access resource.
     *
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param null|callable          $next     next
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $path = $request->getUri()->getPath();

        if (!$this->loggedIn() && !in_array($path, $this->aclData[self::GUEST_LEVEL])) {
            $this->flash->error('You need to be logged in to access page : ' . $request->getUri()->getPath());
            $this->sessionManager->getStorage()->redirectUri = $request->getUri()->getPath();
            return $response->withStatus(302)->withHeader('Location', '/login/index');
        }

        return $next($request, $response);
    }

    /**
     * @param $request
     * @return bool
     */
    protected function loggedIn()
    {
        return $this->sessionManager->getStorage()->offsetGet('userId');
    }
}
