<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\ChannelCampaignService;
use Illuminate\Http\Request;

class ChannelCampaignController extends FrontController
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 批量更新
     */
    public function batchUpdate(Request $request){
        $data = $request->post();

        $channelCampaignService = new ChannelCampaignService();
        $ret = $channelCampaignService->batchUpdate($data);

        return $this->ret($ret);
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 列表
     */
    public function select(Request $request){
        $param = $request->post();

        $channelCampaignService = new ChannelCampaignService();
        $data = $channelCampaignService->select($param);

        return $this->success($data);
    }
}
