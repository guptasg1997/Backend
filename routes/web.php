<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     //return $router->app->version();
//     echo"welcome";
// });
// now we have to add middle ware

//$router->post('/signup', 'ExampleController@create');

$router->group(['middleware' => 'jwtLogin'], function () use ($router) {

    $router->get('/test', 'ExampleController@test'); 

    $router->post('/delete', 'ExampleController@destroy');

    $router->post('/update', 'ExampleController@update');

    $router->post('/alluser', 'ExampleController@alluser');

    $router->post('/create-user', 'ExampleController@create_user');

    $router->post('/add-task' , 'TaskController@add_task');

    $router->post('/all-task' , 'TaskController@all_task');

    $router->post('/piechart' , 'TaskController@piechart');

    $router->post('/update-task' , 'TaskController@update_task');

    $router->post('/view-task' , 'TaskController@view_task');

    $router->post('/delete-task' , 'TaskController@delete_task');

    $router->post('/complete-task' , 'TaskController@complete_task');

    $router->post('/active-task' , 'TaskController@active_task');

    $router->post('/get-task' , 'TaskController@get_task');

    $router->post('/submit-progress' , 'TaskController@submit_progress');


});

$router->group(['middleware' => 'jwtAdmin'], function () use ($router) {

});

$router->post('/signup', 'ExampleController@create');

$router->post('/changepassword', 'ExampleController@changepassword');

//$router->get('/test', 'ExampleController@test'); 

//$router->post('/alluser', 'ExampleController@alluser');

//$router->post('/delete', 'ExampleController@destroy');

//$router->post('/update', 'ExampleController@update');

$router->post('/login', 'ExampleController@login');    

$router->post('/verify_request', 'ExampleController@verify_request');

$router->post('/verify', 'ExampleController@verify');

$router->post('/forgotpassword', 'ExampleController@forgotpassword');

//$router->post('/create_user', 'ExampleController@create_user');

//$router->post('/view-task' , 'TaskController@view_task');

//$router->post('/complete-task' , 'TaskController@complete_task');

//$router->post('/delete-task' , 'TaskController@delete_task');

//$router->post('/active-task' , 'TaskController@active_task');

//$router->post('/update-task' , 'TaskController@update_task');

//$router->post('/piechart' , 'TaskController@piechart');

//$router->post('/get-task' , 'TaskController@get_task');

//$router->post('/all-task' , 'TaskController@all_task');
    


