<?php

/**
 * Define routes here.
 *
 * Routes follow this format:
 *
 * [METHOD, ROUTE, CALLABLE] or
 * [METHOD, ROUTE, [Class => method]]
 *
 * When controller is used without method (as string), it needs to have a magic __invoke method defined.
 *
 * Routes can use optional segments and regular expressions. See nikic/fastroute
 */

return [
    ['GET', '/', 'Kptask\Core\Controller\IndexController'],
   [['GET', 'POST'], '/login/{action}', 'Kptask\Core\Controller\LoginController'],
    [['GET', 'POST'], '/register/{action}', 'Kptask\Core\Controller\RegisterController'],
   [['GET', 'POST'], '/user/{action}[/{userId}]', 'Kptask\User\Controller\UserController'],

];
