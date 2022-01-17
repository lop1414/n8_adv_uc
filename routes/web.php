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

    //UC
    $router->group(['prefix' => 'uc'], function () use ($router) {
        // 推广计划
        $router->group(['prefix' => 'adgroup'], function () use ($router) {
            $router->post('select', 'Admin\Uc\AdgroupController@select');
            $router->post('get', 'Admin\Uc\AdgroupController@get');
            $router->post('read', 'Admin\Uc\AdgroupController@read');
        });
        // 推广单元
        $router->group(['prefix' => 'campaign'], function () use ($router) {
            $router->post('select', 'Admin\Uc\CampaignController@select');
            $router->post('get', 'Admin\Uc\CampaignController@get');
            $router->post('read', 'Admin\Uc\CampaignController@read');
        });
        // 推广单元扩展
        $router->group(['prefix' => 'campaign_extend'], function () use ($router) {
            $router->post('batch_update', 'Admin\Uc\CampaignExtendController@batchUpdate');
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

        // UC同步
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


// 前台接口

$router->group([
    'prefix' => 'front',
    'middleware' => ['api_sign_valid', 'access_control_allow_origin']
], function () use ($router) {
    // 转化
    $router->group(['prefix' => 'convert'], function () use ($router) {
        $router->post('match', '\\App\Common\Controllers\Front\ConvertController@match');
    });

    // 转化回传
    $router->group(['prefix' => 'convert_callback'], function () use ($router) {
        $router->post('get', '\\App\Common\Controllers\Front\ConvertCallbackController@get');
    });

    // 渠道-推广单元
    $router->group(['prefix' => 'channel_campaign'], function () use ($router) {
        $router->post('select', 'Front\ChannelAdgroupController@select');
        $router->post('batch_update', 'Front\ChannelAdgroupController@batchUpdate');
    });
});

$router->group(['middleware' => ['access_control_allow_origin']], function () use ($router) {
    // 点击
    $router->get('front/click', 'Front\AdvClickController@index');
});

$router->post('test', 'TestController@test');

