<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../scripts/debugging.php';
require __DIR__ . '/../scripts/mysql_manager.php';
require __DIR__ . '/../scripts/global_methods.php';

// DEFINES
define('DEBUG', 'true');

$sql_manager = new MySqlManager("localhost", "root", "", "workdb");

$app = AppFactory::create();
$app->setBasePath('/php_login_server');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$app->get('/login', function (Request $request, Response $response) {

    $args = $request->getQueryParams();
    $login = $args['login'];
    $password = $args['password'];

    if (!check_params($login, $password)) {
        $response->getBody()->write(json_encode(
                array(
                    'status' => 0)
            )
        );
        return $response;
    }

    $response->getBody()->write(json_encode(
        array(
            'status' => 1
        )
    ));
    return $response;
});

$app->get('/register', function (Request $request, Response $response) {

    $args = $request->getQueryParams();
    $login = $args['login'];
    $password = $args['password'];
    $email = $args['email'];
    $user_type = $args['type'];

    if (DEBUG) {
        console_log('login ' . $login);
        console_log('pas ' . $password);
        console_log('email ' . $email);
        console_log('type ' . $user_type);
    }

    if (!check_params($login, $password, $email, $user_type)) {
        return add_status($response, 0);
    }

    return add_status($response, 1);
});


$app->post('/create_task', function (Request $request, Response $response) use($sql_manager) {
    $params = (array)$request->getParsedBody();

    $contractor_login = $params['login'];
    $task_info = $params['task_info'];

    if (!check_params($contractor_login, $task_info))
        return add_status($response, 0);

    $user = $sql_manager->get_user($contractor_login);

    if ($user == false){
        print "Could not get user from DB";
        return add_status($response, 0);
    }

    $response->getBody()->write($user['login']);
    return $response;
});


$app->run();
