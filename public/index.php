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

$app->post('/login', function (Request $request, Response $response) use($sql_manager){

    $args = (array)$request->getParsedBody();

    $login = $args['login'];
    $password = $args['password'];

    if (!check_params($login, $password)) {
        return add_status($response, 0);
    }

    $user = $sql_manager->get_user($login);

    if ($user == false){
        print "Could not extract user from db";
        return add_status($response, 0);
    }

    $hash = $user['password'];

    if (password_verify($password, $hash)){
        return add_status($response, 1);
    }

    print "Password is incorrect";
    return add_status($response, 0);
});

$app->post('/register', function (Request $request, Response $response) use($sql_manager){

    $args = (array)$request->getParsedBody();

    $login = $args['login'];
    $password = $args['password'];
    $email = $args['email'];
    $user_type = $args['user_type'];


    if (!check_params($login, $password, $email, $user_type)) {
        print "Could not parse params";
        return add_status($response, 0);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if($sql_manager->register_user($login, $hash, $email, $user_type) == false){
        print "Could not register user";
        return add_status($response, 0);
    }
    print "Successful";
    return add_status($response, 1);
});


$app->post('/create_task', function (Request $request, Response $response) use($sql_manager) {
    $params = (array)$request->getParsedBody();

    $contractor_login = $params['login'];
    $task_info = $params['task_info'];

    if (!check_params($contractor_login, $task_info))
        return add_status($response, 0);

    if ($sql_manager->create_task($contractor_login, $task_info) == false)
        $response = add_status($response, 0);
    else
        $response = add_status($response, 1);
    return $response;
});

/*$app->post('/create_group', function (Request $request, Response $response){


});*/

$app->run();
