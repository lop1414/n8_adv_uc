<?php
namespace App\Http\Controllers\Admin\Uc;


use App\Common\Models\ConvertCallbackStrategyModel;
use App\Models\Uc\UcCampaignModel;

class CampaignController extends UcController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UcCampaignModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        parent::selectPrepare();
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                // 筛选渠道
                $channelId = $this->curdService->requestData['channel_id'] ?? '';
                if($channelId){
                    $builder->whereRaw("id IN (
                        SELECT campaign_id FROM channel_campaigns
                            WHERE channel_id = {$channelId}
                    )");
                }
            });
        });
        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                if(!empty($item->uc_campaign_extends)){
                    $item->convert_callback_strategy = ConvertCallbackStrategyModel::find($item->uc_campaign_extends->convert_callback_strategy_id);
                }else{
                    $item->convert_callback_strategy = null;
                }
                $item->channel_campaign;
                $item->uc_adgroup;
                $item->uc_account;
                $item->admin_name = $this->adminMap[$item->baidu_account->admin_id]['name'];
            }
        });

    }


    /**
     * 列表预处理
     */
    public function getPrepare(){
        parent::getPrepare();

    }

}
