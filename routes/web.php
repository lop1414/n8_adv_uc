<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// 后台
$router->group([
    'prefix' => 'admin',
    'middleware' => ['center_menu_auth', 'admin_request_log', 'access_control_allow_origin']
], function () use ($router) {

    // 账户
    $router->group(['prefix' => 'uc_account'], function () use ($router) {
        $router->post('create', 'Admin\Uc\AccountController@create');
        $router->post('update', 'Admin\Uc\AccountController@update');
        $router->post('select', 'Admin\Uc\AccountController@select');
        $router->post('get', 'Admin\Uc\AccountController@get');
        $router->post('read', 'Admin\Uc\AccountController@read');
        $router->post('enable', 'Admin\Uc\AccountController@enable');
        $router->post('disable', 'Admin\Uc\AccountController@disable');
        $router->post('sync', 'Admin\Uc\AccountController@syncAccount');
        $router->post('batch_update_admin', 'Admin\Uc\AccountController@batchUpdateAdmin');
    });

    //百度
    $router->group(['prefix' => 'baidu'], function () use ($router) {
        // 推广计划
        $router->group(['prefix' => 'campaign'], function () use ($router) {
            $router->post('select', 'Admin\BaiDu\CampaignController@select');
            $router->post('get', 'Admin\BaiDu\CampaignController@get');
            $router->post('read', 'Admin\BaiDu\CampaignController@read');
        });
        // 推广单元
        $router->group(['prefix' => 'adgroup'], function () use ($router) {
            $router->post('select', 'Admin\BaiDu\AdgroupController@select');
            $router->post('get', 'Admin\BaiDu\AdgroupController@get');
            $router->post('read', 'Admin\BaiDu\AdgroupController@read');
        });
        // 推广单元扩展
        $router->group(['prefix' => 'adgroup_extend'], function () use ($router) {
            $router->post('batch_update', 'Admin\BaiDu\AdgroupExtendController@batchUpdate');
        });

    });

    // 点击
    $router->group(['prefix' => 'click'], function () use ($router) {
        $router->post('select', 'Admin\ClickController@select');
        $router->post('callback', 'Admin\ClickController@callback');
    });

    // 转化回传
    $router->group(['prefix' => 'convert_callback'], function () use ($router) {
        $router->post('callback', '\\App\Common\Controllers\Admin\ConvertCallbackController@callback');
    });

    // 任务
    $router->group(['prefix' => 'task'], function () use ($router) {
        $router->post('select', '\\App\Common\Controllers\Admin\TaskController@select');
        $router->post('open', '\\App\Common\Controllers\Admin\TaskController@open');
        $router->post('close', '\\App\Common\Controllers\Admin\TaskController@close');
    });

    // 子任务
    $router->group(['prefix' => 'sub_task'], function () use ($router) {

        // 百度同步
        $router->group(['prefix' => 'baidu_sync'], function () use ($router) {
            $router->post('select', 'Admin\SubTask\TaskBaiDuSyncController@select');
            $router->post('read', 'Admin\SubTask\TaskBaiDuSyncController@read');
        });
    });

    // 回传策略
    $router->group(['prefix' => 'convert_callback_strategy'], function () use ($router) {
        $router->post('create', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@create');
        $router->post('update', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@update');
        $router->post('select', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@select');
        $router->post('get', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@get');
        $router->post('read', '\\App\Common\Controllers\Admin\ConvertCallbackStrategyController@read');
    });
});
