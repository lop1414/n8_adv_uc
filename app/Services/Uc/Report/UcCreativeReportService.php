<?php

namespace App\Services\Uc\Report;


use App\Enums\Uc\UcUnitOfTimeEnum;
use App\Models\Uc\Report\UcCreativeReportModel;

class UcCreativeReportService extends UcReportService
{

    public $modelClass = UcCreativeReportModel::class;


    /**
     * @var
     * 缓存前缀
     */
    protected $prefix = 'creative_report';



    public function makeTaskId($date,$accountId){
        $info = $this->sdk->getCreativeReport([
            'date' => $date,
            'unit_of_time' => UcUnitOfTimeEnum::HOUR
        ]);

        $this->setTaskId($date,$accountId,$info['taskId']);
        return true;
    }




    public function saveTaskData($taskId){
        $data = $this->sdk->getCreativeTaskData($taskId);
        $saveData = [];
        foreach ($data as $item) {
            $dateTime = date('Y-m-d',strtotime($item['date'])).' '.substr($item['time'],0,2).':00:00';
            $saveData[] = [
                'account_id' => $item['account_id'],
                'adgroup_id' => $item['adgroup_id'],
                'campaign_id' => $item['campaign_id'],
                'creative_id' => $item['creative_id'],
                'stat_datetime' => $dateTime,
                'consume' => $item['consume'] * 100,
                'srch' => $item['srch'],
                'click' => $item['click'],
                'binding_conversion' => $item['binding_conversion'],
                'extends' => json_encode($item),
            ];
        }
        if(empty($saveData))  return true;


        $this->batchSave($this->modelClass,$saveData,20,false);
        return true;

    }




}
