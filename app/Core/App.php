<?php

namespace App\Core;

class App
{
    public function run(): void
    {
        $router = new Router();

        // Public routes
        $router->get('/', 'ProjectsController@index');
        $router->get('/project/{slug}', 'ProjectsController@show');
        $router->get('/category/{slug}', 'CategoriesController@show');
        $router->get('/tag/{slug}', 'TagsController@show');

        // Admin routes
        $router->get('/admin/login', 'AuthController@loginForm');
        $router->post('/admin/login', 'AuthController@login');
        $router->post('/admin/logout', 'AuthController@logout');
        // Admin Dashboard
        $router->get('/admin', 'AdminController@dashboard');
        // Admin Settings
        $router->get('/admin/settings', 'AdminSettingsController@edit');
        $router->post('/admin/settings', 'AdminSettingsController@update');
        // Admin Projects
        $router->get('/admin/projects', 'AdminProjectsController@index');
        $router->get('/admin/projects/create', 'AdminProjectsController@create');
        $router->post('/admin/projects', 'AdminProjectsController@store');
        $router->get('/admin/projects/{id}/edit', 'AdminProjectsController@edit');
        $router->post('/admin/projects/{id}', 'AdminProjectsController@update');
        $router->post('/admin/projects/{id}/delete', 'AdminProjectsController@destroy');
        // Admin Categories
        $router->get('/admin/categories', 'AdminCategoriesController@index');
        $router->get('/admin/categories/create', 'AdminCategoriesController@create');
        $router->post('/admin/categories', 'AdminCategoriesController@store');
        $router->get('/admin/categories/{id}/edit', 'AdminCategoriesController@edit');
        $router->post('/admin/categories/{id}', 'AdminCategoriesController@update');
        $router->post('/admin/categories/{id}/delete', 'AdminCategoriesController@destroy');
        // Admin Tags
        $router->get('/admin/tags', 'AdminTagsController@index');
        $router->get('/admin/tags/create', 'AdminTagsController@create');
        $router->post('/admin/tags', 'AdminTagsController@store');
        $router->get('/admin/tags/{id}/edit', 'AdminTagsController@edit');
        $router->post('/admin/tags/{id}', 'AdminTagsController@update');
        $router->post('/admin/tags/{id}/delete', 'AdminTagsController@destroy');

        $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }
}