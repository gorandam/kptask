<?php

$guest = [
    '/',
    '/register/index',
    '/register/create',
    '/login/index',
    '/login/login',
];

$level2 = [
    '/user/index',
];

$level1 = [
    '/login/logout',
    '/user/form*',
    '/user/delete*',
    '/',
];

//can also see everything level2 can see
$level1 = array_merge($level2, $level1);

return [
    0 => $guest,
    1 => $level1,
    2 => $level2
];
