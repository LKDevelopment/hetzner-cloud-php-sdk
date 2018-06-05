<?php

$router->group(['prefix' => 'servers'], function ($route) {

    $route->group(['prefix' => '{server_id}'], function ($route) {
        $route->get('/', function ($id) {
            return response(file_get_contents(RESPONSE_PATH . 'servers/one.json'));
        });
        $route->put('/', function ($id) {
            return response(file_get_contents(RESPONSE_PATH . 'servers/change_name.json'));
        });
    });
    $route->get('/', function () {
        return response(file_get_contents(RESPONSE_PATH . 'servers/all.json'));
    });
});