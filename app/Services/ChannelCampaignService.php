<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\UnionApiService;
use App\Common\Tools\CustomException;
use App\Models\ChannelCampaignLogModel;
use App\Models\ChannelCampaignModel;
use App\Models\Uc\UcAccountModel;
use App\Models\Uc\UcCampaignModel;
use App\Models\Uc\UcCreativeModel;
use Illuminate\Support\Facades\DB;

class ChannelCampaignService extends BaseService
{
    /**
     * @param $data
     * @return bool
     * @throws CustomException
     * 批量更新
     */
    public function batchUpdate($data){
        $this->validRule($data, [
            'channel_id'    => 'required|integer',
            'campaign_ids'  => 'required|array',
            'channel' => 'required',
            'platform' => 'required'
        ]);

        Functions::hasEnum(PlatformEnum::class, $data['platform']);

        DB::beginTransaction();

        try{
            foreach($data['campaign_ids'] as $campaignId){
                $this->update([
                    'campaign_id' => $campaignId,
                    'channel_id' => $data['channel_id'],
                    'platform' => $data['platform'],
                    'extends' => [
                        'channel' => $data['channel'],
                    ],
                ]);
            }
        }catch(CustomException $e){
            DB::rollBack();
            throw $e;
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return true;
    }

    /**
     * @param $data
     * @return bool
     * 更新
     */
    public function update($data){
        $channelCampaignModel = new ChannelCampaignModel();
        $channelCampaign = $channelCampaignModel->where('campaign_id', $data['campaign_id'])
            ->where('platform', $data['platform'])
            ->first();

        $flag = $this->buildFlag($channelCampaign);
        if(empty($channelCampaign)){
            $channelCampaign = new ChannelCampaignModel();
        }

        $channelCampaign->campaign_id = $data['campaign_id'];
        $channelCampaign->channel_id = $data['channel_id'];
        $channelCampaign->platform = $data['platform'];
        $channelCampaign->extends = $data['extends'];
        $ret = $channelCampaign->save();
        if($ret && !empty($channelCampaign->campaign_id) && $flag != $this->buildFlag($channelCampaign)){
            $this->createChannelAdLog([
                'channel_campaign_id' => $channelCampaign->id,
                'campaign_id' => $data['campaign_id'],
                'channel_id' => $data['channel_id'],
                'platform'   => $data['platform'],
                'extends'    => $data['extends']
            ]);
        }

        return $ret;
    }

    /**
     * @param $channelCampaign
     * @return string
     * 构建标识
     */
    protected function buildFlag($channelCampaign){
        $adminId = !empty($channelCampaign->extends->channel->admin_id) ? $channelCampaign->extends->channel->admin_id : 0;
        if(empty($channelCampaign)){
            $flag = '';
        }else{
            $flag = implode("_", [
                $channelCampaign->adgroup_id,
                $channelCampaign->channel_id,
                $channelCampaign->platform,
                $adminId
            ]);
        }
        return $flag;
    }

    /**
     * @param $data
     * @return bool
     * 创建渠道-计划日志
     */
    protected function createChannelAdLog($data){
        $channelCampaignLogModel = new ChannelCampaignLogModel();
        $channelCampaignLogModel->channel_campaign_id = $data['channel_campaign_id'];
        $channelCampaignLogModel->campaign_id = $data['campaign_id'];
        $channelCampaignLogModel->channel_id = $data['channel_id'];
        $channelCampaignLogModel->platform = $data['platform'];
        $channelCampaignLogModel->extends = $data['extends'];
        return $channelCampaignLogModel->save();
    }

    /**
     * @param $param
     * @return array
     * @throws CustomException
     * 列表
     */
    public function select($param){
        $this->validRule($param, [
            'start_datetime' => 'required',
            'end_datetime' => 'required',
        ]);

        $channelCampaignModel = new ChannelCampaignModel();
        $channelCampaigns = $channelCampaignModel->whereBetween('updated_at', [$param['start_datetime'], $param['end_datetime']])->get();

        $distinct = $data = [];
        foreach($channelCampaigns as $channelCampaign){
            if(empty($distinct[$channelCampaign['channel_id']])){
                // 推广计划
                $ucCampaign = UcCampaignModel::find($channelCampaign['campaign_id']);
                if(empty($ucCampaign)){
                    continue;
                }

                // 账户
                $ucAccount = (new UcAccountModel())->where('account_id', $ucCampaign['account_id'])->first();
                if(empty($ucAccount)){
                    continue;
                }

                $data[] = [
                    'channel_id' => $channelCampaign['channel_id'],
                    'ad_id' => $channelCampaign['ad_id'],
                    'ad_name' => $channelCampaign['name'],
                    'account_id' => $channelCampaign['account_id'],
                    'account_name' => $ucAccount['name'],
                    'admin_id' => $ucAccount['admin_id'],
                ];
                $distinct[$channelCampaign['channel_id']] = 1;
            }
        }

        return $data;
    }




    /**
     * @param $param
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($param){
        $date = $param['date'];

        $startTime = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($date)));
        $endTime = "{$date} 23:59:59";

        $lastMaxId = 0;
        do{

            $ucCreatives = (new UcCreativeModel())
                ->where('id','>',$lastMaxId)
                ->whereBetween('updated_at', [$startTime, $endTime])
                ->skip(0)
                ->take(1000)
                ->orderBy('id')
                ->get();

            $keyword = 'sign='. Advs::getAdvClickSign(AdvAliasEnum::UC);

            foreach($ucCreatives as $ucCreative){
                $lastMaxId = $ucCreative['id'];

                $clickMonitorUrl= $ucCreative->extends['clickMonitorUrl'] ?? '';
                if(empty($clickMonitorUrl)) continue;
                if(strpos($clickMonitorUrl, $keyword) === false) continue;

                $ret = parse_url($clickMonitorUrl);
                parse_str($ret['query'], $param);

                $unionApiService = new UnionApiService();

                if(!empty($param['android_channel_id'])){
                    $channel = $unionApiService->apiReadChannel(['id' => $param['android_channel_id']]);
                    $channelExtends = $channel['channel_extends'] ?? [];
                    $channel['admin_id'] = $channelExtends['admin_id'] ?? 0;
                    unset($channel['extends']);
                    unset($channel['channel_extends']);

                    $this->update([
                        'campaign_id' => $ucCreative->campaign_id,
                        'channel_id' => $param['android_channel_id'],
                        'platform' => PlatformEnum::DEFAULT,
                        'extends' => [
                            'click_monitor_url' => $clickMonitorUrl,
                            'channel' => $channel,
                        ],
                    ]);
                }
            }
        }while(!$ucCreatives->isEmpty());

        return true;
    }
}
