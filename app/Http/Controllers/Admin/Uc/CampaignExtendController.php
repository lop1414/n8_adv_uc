<?php

namespace App\Http\Controllers\Admin\Uc;

use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Common\Models\ConvertCallbackStrategyModel;
use App\Models\Uc\UcCampaignExtendModel;
use App\Models\Uc\UcCampaignModel;
use Illuminate\Http\Request;

class CampaignExtendController extends UcController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UcCampaignExtendModel();

        parent::__construct();
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 批量更新
     */
    public function batchUpdate(Request $request){
        $this->validRule($request->post(), [
            'campaign_ids' => 'required|array',
            'convert_callback_strategy_id' => 'required',
        ]);
        $campaignIds = $request->post('campaign_ids');
        $convertCallbackStrategyId = $request->post('convert_callback_strategy_id');

        // 回传规则是否存在
        $convertCallbackStrategyModel = new ConvertCallbackStrategyModel();
        $strategy = $convertCallbackStrategyModel->find($convertCallbackStrategyId);
        if(empty($strategy)){
            throw new CustomException([
                'code' => 'NOT_FOUND_CONCERT_CALLBACK_STRATEGY',
                'message' => '找不到对应回传策略',
            ]);
        }

        if($strategy->status != StatusEnum::ENABLE){
            throw new CustomException([
                'code' => 'CONCERT_CALLBACK_STRATEGY_IS_NOT_ENABLE',
                'message' => '该回传策略已被禁用',
            ]);
        }

        $campaigns = [];
        foreach($campaignIds as $campaignId){
            $campaign = UcCampaignModel::find($campaignId);
            if(empty($campaign)){
                throw new CustomException([
                    'code' => 'NOT_FOUND_AD',
                    'message' => "找不到该计划{{$campaign}}",
                ]);
            }
            $campaigns[] = $campaign;
        }

        foreach($campaigns as $campaign){
            $ucCampaignExtend = UcCampaignExtendModel::find($campaign->id);

            if(empty($ucCampaignExtend)){
                $ucCampaignExtend = new UcCampaignExtendModel();
                $ucCampaignExtend->campaign_id = $campaign->id;
            }

            $ucCampaignExtend->convert_callback_strategy_id = $convertCallbackStrategyId;
            $ucCampaignExtend->save();
        }

        return $this->success();
    }
}
