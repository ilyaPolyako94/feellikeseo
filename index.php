<?php
/**
 * Created by PhpStorm.
 * User: ilyapolyakov
 * Date: 2019-02-26
 * Time: 09:21
 */
require 'autoload.php';
require_once 'Controllers/Controller.php';
session_start();

use Core\App;

$app = new App();

if(isset($_SERVER['REQUEST_URI'])){
    if($_SERVER['REQUEST_URI'] === '/') {
        $server = '/';
    }else{
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);;
        $server = $app->path_split($path);
    }
}else{
    $server = '/';
}
switch (App::is_home($server))
{

    case true:
            require_once 'Controllers/IndexController.php';
            require_once 'Models/User.php';
            $controller = new Controllers\IndexController();
            print $controller->index();
            break;
    case false:

        $app->controller = $server[1];

        $app->model = $server[1];

        $app->method = isset($server[2])? $server[2] :'';

        $app->param = array_slice($server, 3);

        $controller_exists = __DIR__.'/Controllers/'.$app->controller.'Controller.php';
        if (file_exists($controller_exists))
        {
            $model = ucfirst($app->model);
            $modelPath = __DIR__.'/Models/'.$model.'.php';
            $controllerPath = __DIR__.'/Controllers/'.ucfirst($app->controller).'Controller.php';
            //require_once __DIR__.'/Models/'.$app->model.'Model.php';
            require_once 'Models/User.php';
            if(file_exists($modelPath)) require_once $modelPath;
            require_once $controllerPath;

            $controller = 'Controllers\\'.ucfirst($app->controller).'Controller';
            //$Model = new $model;
            $Controller = new $controller;

            $method = $app->method;
            if ($method != '')
            {
                print $Controller->$method($app->param);
            }
            else
            {
                print $Controller->index();
            }
        }
        else
        {
            header('HTTP/1.1 404 Not Found');
            die('404 - The file - '.$app->controller.' - not found');
        }
        break;
    default:
        print 'An Error Occured';
        break;
}
