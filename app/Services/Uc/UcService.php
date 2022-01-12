<?php

namespace App\Services\Uc;

use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Enums\RemarkStatusEnum;
use App\Enums\Uc\UcCampaignStatusEnum;
use App\Models\Uc\Report\UcAccountReportModel;
use App\Models\Uc\UcAccountModel;
use App\Models\Uc\UcAdgroupModel;
use App\Models\Uc\UcCampaignModel;
use App\Models\Uc\UcCreativeModel;
use App\Sdks\Uc\Uc;
use Illuminate\Support\Facades\DB;


class UcService extends BaseService
{

    /**
     * @var Uc
     * 句柄
     */
    public $sdk;

    /**
     * @var
     * 管家账户信息
     */
    protected $manageAccount;


    /**
     * @var
     * 账户ID映射
     */
    protected $accountMap;




    /**
     * BaiDuService constructor.
     * @param array $manageAccount
     */
    public function __construct($manageAccount = []){
        parent::__construct();

        if(!empty($manageAccount)) $this->setManageAccount($manageAccount);

    }



    public function setSdk($accountName,$password,$token){
        $this->sdk = new Uc($accountName,$password,$token);
        return $this;
    }



    public function setManageAccount($info){
        $this->manageAccount = $info;
        $this->setSdk($this->manageAccount['name'],$this->manageAccount['password'],$this->manageAccount['token']);
        return $this;
    }


    /**
     * @param $name
     * @return mixed
     * 通过账户名称
     */
    public function getAccountByName($name){
        if(!isset($this->accountIdMap[$name])){
            $info = (new UcAccountModel())->where('name',$name)->first();
            $this->setAccountMap($info);
        }
        return $this->accountMap[$name];
    }


    public function setAccountMap($info){
        $this->accountMap[$info['name']] = $info;
    }




    /**
     * @param $modelClass
     * @param $data
     * @param int $number
     * @param bool $isIncludePrimaryKey 是否包含主键
     * @return bool
     * 批量保存
     */
    public function batchSave($modelClass,$data,$number = 20,$isIncludePrimaryKey = true){
        $model = new $modelClass();
        if($isIncludePrimaryKey){
            $model->chunkInsertOrUpdate($data, $number, $model->getTable(), $model->getTableColumnsWithPrimaryKey());
        }else{
            $model->chunkInsertOrUpdate($data, $number, $model->getTable());
        }
        return true;
    }




    public function sync($option){
        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        $accountList = array_merge($this->getSubAccountGroup($accountIds),$this->getNoSubAccountGroup($accountIds));
        foreach ($accountList as $groups){
            $this->setSdk($groups['name'],$groups['password'],$groups['token']);

            // 并发分片大小
            if(!empty($option['multi_chunk_size'])){
                $multiChunkSize = min(intval($option['multi_chunk_size']), 8);
                $this->sdk->setMultiChunkSize($multiChunkSize);
            }

            $this->syncItem($groups['list']);

        }
    }



    public function syncItem($subAccount){
        throw new CustomException([
            'code' => 'METHOD_NOT_IMPLEMENTED',
            'message' => '请实现 syncItem 方法',
        ]);
    }



    /**
     * @param $item
     * 推广组不存在 异常处理 - 更新备注状态
     */
    public function handleAdgroupIdNotExists($item){

        foreach($item['data']['header']['failures'] as $failure){

            if(!$this->sdk->isAdgroupIdNotExistsByCode($failure['code'])){
                continue;
            }

            $adgroup = (new UcAdgroupModel())
                ->where('id',$failure['id'])
                ->first();
            if(empty($adgroup)) continue;

            $adgroup->remark_status =  RemarkStatusEnum::DELETE;
            $adgroup->save();

            $campaigns = (new UcCampaignModel())
                ->where('adgroup_id',$failure['id'])
                ->get();

            foreach ($campaigns as $campaign){

                $campaign->remark_status =  RemarkStatusEnum::DELETE;
                $campaign->save();

                (new UcCreativeModel())
                    ->where('adgroup_id',$campaign['id'])
                    ->update(['remark_status' => RemarkStatusEnum::DELETE]);
            }
        }
    }




    /**
     * @return mixed
     * 获取在跑账户id
     */
    public function getRunningAccountIds(){
        // 在跑状态
        $runningStatus = [
            UcCampaignStatusEnum::CAMPAIGN_STATUS_DELIVERY_OK,
            UcCampaignStatusEnum::CAMPAIGN_STATUS_BUDGET_EXCEED,
            UcCampaignStatusEnum::CAMPAIGN_STATUS_PRE_OFFLINE_BUDGET,
        ];
        $runningStatusStr = implode("','", $runningStatus);

        $ucAccountModel = new UcAccountModel();
        $ucAccountIds = $ucAccountModel->whereRaw("
            account_id IN (
                SELECT account_id FROM uc_campaigns
                    WHERE `status` IN ('{$runningStatusStr}')
                    GROUP BY account_id
            )
        ")->pluck('account_id');

        return $ucAccountIds->toArray();
    }


    /**
     * @param $accountIds
     * @return mixed
     * 获取存在历史消耗账户
     */
    public function getHasHistoryCostAccount($accountIds){
        $today = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-3 days', strtotime($today)));

        $ucAccountReportModel = new UcAccountReportModel();
        $builder = $ucAccountReportModel->whereBetween('stat_datetime', ["{$startDate} 00:00:00", "{$today} 23:59:59"]);

        if(!empty($accountIds)){
            $builder->whereIn('account_id', $accountIds);
        }

        $report = $builder->groupBy('account_id')
            ->orderBy('consume', 'DESC')
            ->select(DB::raw("account_id, SUM(consume) consume"))
            ->pluck('account_id');

        return $report->toArray();
    }



    /**
     * @param array $accountIds
     * @return array
     * 获取没有子账户的账户信息
     */
    public function getNoSubAccountGroup(array $accountIds = []){
        $ucAccountModel = new UcAccountModel();
        $builder = $ucAccountModel->where('status', StatusEnum::ENABLE);

        if(!empty($accountIds)){
            $builder->whereIn('account_id',$accountIds);
        }

        $accounts = $builder->where('parent_id', 0)->get();

        if($accounts->isEmpty()) return [];
        $group = [];
        foreach ($accounts as $account){
            $group[$account->account_id] = $account->toArray();
            $group[$account->account_id]['list'][] = $account;
        }
        return $group;
    }


    /**
     * @param array $accountIds
     * @return array
     * 获取子账号组
     */
    public function getSubAccountGroup(array $accountIds = []){
        $subAccount = $this->getSubAccount($accountIds);

        $s = [];
        foreach($subAccount as $account){
            $s[$account->parent_id][] = $account;
        }


        $group = [];
        foreach($s as $accountId => $ss){
            $tmp = (new UcAccountModel())
                ->where('account_id',$accountId)
                ->first();

            if(empty($tmp)) continue;
            $group[$accountId] = $tmp->toArray();
            $group[$accountId]['list'] = $ss;
        }

        return $group;
    }



    /**
     * @param array $accountIds
     * @return mixed
     * 获取子账号
     */
    public function getSubAccount(array $accountIds = []){
        $ucAccountModel = new UcAccountModel();
        $builder = $ucAccountModel->where('status', StatusEnum::ENABLE);

        if(!empty($accountIds)){
            $accountIdsStr = implode("','", $accountIds);
            $builder->whereRaw("
                (
                    account_id IN ('{$accountIdsStr}')
                    OR parent_id IN ('{$accountIdsStr}')
                )
            ");
        }

        $subAccount = $builder->where('parent_id', '<>', 0)->get();

        return $subAccount;
    }


    /**
     * @param $accounts
     * @param array $param
     * @param int $pageSize
     * @return array
     * 并发获取分页列表
     */
    public function multiGetPageList($accounts, $param = [],$pageSize = 200){

        // 账户第一页数据
        $accountNames = [];
        foreach($accounts as $account){
            $accountNames[] = $account['name'];
        }
        $res = $this->sdkMultiGetList($accountNames,$param,1,$pageSize);

        $data = [];
        foreach($res as $v){
            // 读取csv文件信息

        }

        return $data;
    }


    public function sdkMultiGetList($accountNames,$param,$page,$pageSize){}


    /**
     * @param $accounts
     * @return array
     * 获取账户下的推广组
     */
    public function getAdgroupParamByAccount($accounts){
        $params = [];
        foreach ($accounts as $account){
            // 获取计划ID
            $adgroupIds = [];

            $adgroups = (new UcAdgroupModel())
                ->where('account_id',$account['account_id'])
                ->where('remark_status','!=',RemarkStatusEnum::DELETE)
                ->get();

            foreach ($adgroups as $adgroup){
                $this->setAccountMap($account);
                $adgroupIds[] = $adgroup['id'];
            }

            if(empty($adgroupIds)) continue;

            $params[] = [
                'adgroup_ids'      => $adgroupIds,
                'account_name'      => $account['name']
            ];
        }
        return $params;
    }


    /**
     * @param $accounts
     * @return array
     * 获取账户下的推广计划
     */
    public function getCampaignParamByAccount($accounts){
        $params = [];
        foreach ($accounts as $account){
            // 获取计划ID
            $campaignIds = [];

            $campaigns = (new UcCampaignModel())
                ->where('account_id',$account['account_id'])
                ->where('remark_status','!=',RemarkStatusEnum::DELETE)
                ->get();

            foreach ($campaigns as $campaign){
                $this->setAccountMap($account);
                $campaignIds[] = $campaign['id'];
            }

            if(empty($campaignIds)) continue;

            $params[] = [
                'campaign_ids'      => $campaignIds,
                'account_name'      => $account['name']
            ];
        }
        return $params;
    }





}
