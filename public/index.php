<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;



require __DIR__ . '/../vendor/autoload.php';
require(__DIR__ . '/../scripts/debugging.php');

// DEFINES
define('DEBUG', 'true');


$app = AppFactory::create();
$app->setBasePath('/php_login_server');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$app->get('/login', function (Request $request, Response $response){

    $args = $request->getQueryParams();
    $login = $args['login'];
    $password = $args['password'];

    if ($login == null || $password == null){
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

$app->get('/register', function (Request  $request, Response $response){

    $args = $request->getQueryParams();
    $login = $args['login'];
    $password = $args['password'];
    $email = $args['email'];
    $user_type = $args['type'];

    if (DEBUG){
        console_log('login '. $login);console_log('pas '. $password);console_log('email '. $email);console_log('type '. $user_type);
    }

    if ($login == null || $password == null || $email == null || $user_type == null){
        $response->getBody()->write(json_encode(
            array(
                'status' => 0,
            )
        ));
        return $response;
    }

    $response->getBody()->write(json_encode(
        array(
            'status' => 1
        )
    ));
    return $response;
});

/*$app->get('/add_task', function (Request $request, Response $response){
    $args = $request->getQueryParams();
});*/

$app->post('/add_task', function (Request $request, Response $response){
    $params = (array)$request->getParsedBody();

    $contractor_login = $params['contractor_login'];
    $task_info = $params['task_info'];

    function get_connection() {
        $link = mysqli_connect("localhost", "root", "");
        if ($link == false){
            print("Could not open connection");
        }else{
            print("Connection was established");
        }
    }



    get_connection();
    console_log($contractor_login);
    $response->getBody()->write($contractor_login);
    return $response;
});



$app->run();
