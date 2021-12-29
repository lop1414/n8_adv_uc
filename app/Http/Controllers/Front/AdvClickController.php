<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\ClickController;
use App\Common\Enums\AdvAliasEnum;

class AdvClickController extends ClickController
{
    public function __construct(){
        parent::__construct(AdvAliasEnum::UC);
    }

    /**
     * @return false|string
     * 广告商响应
     */
    protected function advResponse(){
        return json_encode([
            'code' => 0,
            'message' => 'SUCCESS'
        ]);
    }

}
