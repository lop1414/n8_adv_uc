<?php

namespace App\Services\Uc\Report;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomRedis;
use App\Services\Uc\UcService;
use Illuminate\Support\Facades\DB;

class UcReportService extends UcService
{
    /**
     * @var string
     * 模型类
     */
    public $modelClass;

    /**
     * @var
     * 缓存前缀
     */
    protected $prefix;


    protected $customRedis;


    /**
     * OceanAccountReportService constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
        $this->customRedis = new CustomRedis();
    }



    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        ini_set('memory_limit', '2048M');

        $t = microtime(1);

        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        // 并发分片大小
        if(!empty($option['multi_chunk_size'])){
            $multiChunkSize = min(intval($option['multi_chunk_size']), 8);
            $this->sdk->setMultiChunkSize($multiChunkSize);
        }

        // 在跑账户
        if(!empty($option['running'])){
            $runningAccountIds = $this->getRunningAccountIds();
            if(!empty($accountIds)){
                $accountIds = array_intersect($accountIds, $runningAccountIds);
            }else{
                $accountIds = $runningAccountIds;
            }
        }

        $dateRange = Functions::getDateRange($option['date']);
        $dateList = Functions::getDateListByRange($dateRange);

        // 删除
        if(!empty($option['delete'])){
            $between = [
                $dateRange[0] .' 00:00:00',
                $dateRange[1] .' 23:59:59',
            ];

            $model = new $this->modelClass();

            $builder = $model->whereBetween('stat_datetime', $between);

            if(!empty($accountIds)){
                $builder->whereIn('account_id', $accountIds);
            }

            $builder->delete();
        }

        // 账户消耗
        if(!empty($option['run_by_account_cost'])){
            $accountIds = $this->runByAccountCost($accountIds, $option['date']);
            var_dump($accountIds);
        }

        // 历史消耗
        if(!empty($option['has_history_cost'])){
            $accountIds = $this->getHasHistoryCostAccount($accountIds);
        }

        $accountGroups = array_merge($this->getSubAccountGroup($accountIds),$this->getNoSubAccountGroup($accountIds));

        foreach ($accountGroups as $groups){
            $this->setSdk($groups['name'],$groups['password'],$groups['token']);
            foreach ($groups['list'] as $account){
                foreach($dateList as $date) {
                    $taskId = $this->getTaskId($date,$account['account_id']);
                    //生成报告任务
                    if(!$taskId){
                        $this->makeTaskId($date,$account['account_id']);
                        continue;
                    }

                    //未完成任务
                    if(!$this->sdk->isCompleteTask($taskId)){
                        continue;
                    }

                    $result = $this->saveTaskData($taskId);
                    //删除任务缓存
                    if($result){
                        $this->delTaskId($date,$account['account_id']);
                    }

                }
            }
        }

        $t = microtime(1) - $t;
        Functions::consoleDump($t);

        return true;
    }


    public function makeTaskId($date,$accountId){}


    public function saveTaskData($taskId){}




    /**
     * @param $date
     * @param $accountId
     * @param $taskId
     * @param int $ttl
     * @return mixed
     * 保存任务id(缓存)
     */
    public function setTaskId($date,$accountId,$taskId,$ttl = 7200){
        $key = $this->getTaskKey($date,$accountId);
        $this->customRedis->set($key,$taskId);
        $this->customRedis->expire($key, $ttl);
        return true;
    }


    /**
     * @param $date
     * @param $accountId
     * @return mixed
     * 获取任务ID（缓存）
     */
    public function getTaskId($date,$accountId){
        $key = $this->getTaskKey($date,$accountId);
        return $this->customRedis->get($key);
    }


    /**
     * @param $date
     * @param $accountId
     * @return mixed
     * 删除缓存
     */
    public function delTaskId($date,$accountId){
        $key = $this->getTaskKey($date,$accountId);
        return $this->customRedis->del($key);
    }


    /**
     * @param $date
     * @param $accountId
     * @return string
     * 获取任务下标
     */
    public function getTaskKey($date,$accountId){
        return implode(':',[$this->prefix,$date,$accountId]);
    }



    /**
     * @param $item
     * @return bool
     * 校验
     */
    protected function itemValid($item){
        $valid = true;

        if(
            empty($item['consume']) &&
            empty($item['srch']) &&
            empty($item['click'])
        ){
            $valid = false;
        }

        return $valid;
    }




    /**
     * @param $accountIds
     * @param $date
     * @return mixed
     * 按账户消耗执行
     */
    protected function runByAccountCost($accountIds, $date){
        return $accountIds;
    }



    /**
     * @param string $date
     * @return mixed
     * @throws CustomException
     * 按日期获取账户报表
     */
    public function getAccountReportByDate($date = 'today'){
        $date = Functions::getDate($date);
        Functions::dateCheck($date);

        $model = new $this->modelClass();
        $report = $model->whereBetween('stat_datetime', ["{$date} 00:00:00", "{$date} 23:59:59"])
            ->groupBy('account_id')
            ->orderBy('consume', 'DESC')
            ->select(DB::raw("account_id, SUM(consume) consume"))
            ->get();

        return $report;
    }




}
