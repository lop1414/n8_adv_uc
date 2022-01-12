<?php

namespace App\Services\Uc\Report;


use App\Enums\Uc\UcUnitOfTimeEnum;
use App\Models\Uc\Report\UcAccountReportModel;

class UcAccountReportService extends UcReportService
{

    public $modelClass = UcAccountReportModel::class;

    /**
     * @var
     * 缓存前缀
     */
    protected $prefix = 'account_report';



    public function makeTaskId($date,$accountId){
        $info = $this->sdk->getAccountReportTask([
            'date' => $date,
            'unit_of_time' => UcUnitOfTimeEnum::HOUR
        ]);

        $this->setTaskId($date,$accountId,$info['taskId']);
        return true;
    }


    public function saveTaskData($taskId){
        $data = $this->sdk->getAccountTaskData($taskId);

        $saveData = [];
        foreach ($data as $item) {
            $dateTime = date('Y-m-d',strtotime($item['date'])).' '.substr($item['time'],0,2).':00:00';
            $saveData[] = [
                'account_id' => $item['account_id'],
                'stat_datetime' => $dateTime,
                'consume' => $item['consume'] * 100,
                'srch' => $item['srch'],
                'click' => $item['click'],
                'extends' => json_encode($item),
            ];
        }
        if(empty($saveData)) return true;
        $this->batchSave($this->modelClass,$saveData,20,false);
        return true;
    }




}
