<?php

use Controllers\AdminController;
use Controllers\PartialController;
use Controllers\SiteController;
use Controllers\UserController;

return [
    'home' => [
        'path' => 'index',
        'controller' => SiteController::class,
        'action' => 'home',
        'aliases' => ['', '/', 'index.php'],
    ],
    'admin' => [
        'path' => 'admin',
        'controller' => AdminController::class,
        'action' => 'dashboard',
        'guard' => 'admin',
        'aliases' => ['admin.php'],
    ],
    'add' => [
        'path' => 'add-anime',
        'controller' => AdminController::class,
        'action' => 'addAnime',
        'guard' => 'admin',
        'aliases' => ['añadir', 'añadir.php', 'anadir', 'anadir.php'],
    ],
    'character' => [
        'path' => 'character',
        'controller' => SiteController::class,
        'action' => 'character',
        'aliases' => ['character.php'],
    ],
    'featured' => [
        'path' => 'featured',
        'controller' => SiteController::class,
        'action' => 'featured',
        'aliases' => ['destacados', 'destacados.php'],
    ],
    'detail' => [
        'path' => 'detail',
        'patterns' => ['detail/{detail_ref}'],
        'controller' => SiteController::class,
        'action' => 'detail',
        'aliases' => ['detail.php'],
    ],
    'admin_comments' => [
        'path' => 'manage-comments',
        'controller' => AdminController::class,
        'action' => 'manageComments',
        'guard' => 'admin',
        'aliases' => ['gescom', 'gesCom', 'gesCom.php'],
    ],
    'admin_manage' => [
        'path' => 'manage-catalog',
        'controller' => AdminController::class,
        'action' => 'manageCatalog',
        'guard' => 'admin',
        'aliases' => ['gestion', 'gestion.php'],
    ],
    'admin_users' => [
        'path' => 'manage-users',
        'controller' => AdminController::class,
        'action' => 'manageUsers',
        'guard' => 'admin',
        'aliases' => ['gesus', 'gesUs', 'gesUs.php'],
    ],
    'login' => [
        'path' => 'login',
        'controller' => SiteController::class,
        'action' => 'login',
        'aliases' => ['ingresar', 'ingresar.php'],
    ],
    'payment' => [
        'path' => 'payment',
        'controller' => SiteController::class,
        'action' => 'payment',
        'aliases' => ['pago', 'pago.php', 'premium.php'],
    ],
    'movies' => [
        'path' => 'movies',
        'controller' => SiteController::class,
        'action' => 'movies',
        'aliases' => ['peliculas', 'peliculas.php'],
    ],
    'ranking' => [
        'path' => 'ranking',
        'controller' => SiteController::class,
        'action' => 'ranking',
        'aliases' => ['ranking.php'],
    ],
    'register' => [
        'path' => 'register',
        'controller' => SiteController::class,
        'action' => 'register',
        'aliases' => ['registro', 'registro.php'],
    ],
    'series' => [
        'path' => 'series',
        'controller' => SiteController::class,
        'action' => 'series',
        'aliases' => ['series.php'],
    ],
    'user' => [
        'path' => 'profile',
        'controller' => UserController::class,
        'action' => 'profile',
        'guard' => 'auth',
        'aliases' => ['user', 'user.php', 'perfil'],
    ],
    'partial_layout' => [
        'path' => 'partials/layout',
        'controller' => PartialController::class,
        'action' => 'layout',
        'aliases' => ['partials/layout.php'],
    ],
    'partial_admin_layout' => [
        'path' => 'partials/admin-layout',
        'controller' => PartialController::class,
        'action' => 'adminLayout',
        'guard' => 'admin',
        'aliases' => ['partials/admin-layout.php'],
    ],
    'api_activity' => [
        'path' => 'api/activity',
        'controller' => Controllers\Api\ActivityController::class,
        'action' => 'handle',
        'aliases' => ['api/activity.php'],
    ],
    'api_admin' => [
        'path' => 'api/admin',
        'controller' => Controllers\Api\AdminController::class,
        'action' => 'handle',
        'aliases' => ['api/admin.php'],
    ],
    'api_anime_data' => [
        'path' => 'api/anime_data',
        'controller' => Controllers\Api\AnimeDataController::class,
        'action' => 'handle',
        'aliases' => ['api/anime_data.php'],
    ],
    'api_auth' => [
        'path' => 'api/auth',
        'controller' => Controllers\Api\AuthController::class,
        'action' => 'handle',
        'aliases' => ['api/auth.php'],
    ],
    'api_comments' => [
        'path' => 'api/comments',
        'controller' => Controllers\Api\CommentsController::class,
        'action' => 'handle',
        'aliases' => ['api/comments.php'],
    ],
    'api_jikan_proxy' => [
        'path' => 'api/jikan_proxy',
        'patterns' => ['api/jikan_proxy/{any}'],
        'controller' => Controllers\Api\JikanProxyController::class,
        'action' => 'handle',
        'aliases' => ['api/jikan_proxy.php'],
    ],
    'api_profile' => [
        'path' => 'api/profile',
        'controller' => Controllers\Api\ProfileController::class,
        'action' => 'handle',
        'aliases' => ['api/profile.php'],
    ],
    'api_requests' => [
        'path' => 'api/requests',
        'controller' => Controllers\Api\RequestsController::class,
        'action' => 'handle',
        'aliases' => ['api/requests.php'],
    ],
    'api_save_anime' => [
        'path' => 'api/save_anime',
        'controller' => Controllers\Api\SaveAnimeController::class,
        'action' => 'handle',
        'aliases' => ['api/save_anime.php'],
    ],
    'api_users' => [
        'path' => 'api/users',
        'controller' => Controllers\Api\UsersController::class,
        'action' => 'handle',
        'aliases' => ['api/users.php'],
    ],
    'api_cleanup' => [
        'path' => 'api/cleanup',
        'controller' => Controllers\Api\CleanupController::class,
        'action' => 'handle',
        'aliases' => ['api/cleanup.php'],
    ],
];
