<?php

namespace App\Sdks\Uc\Traits;


trait Report
{

    public function isCompleteTask($taskId){
        return true;
    }


    /**
     * @param $taskId
     * @return false|string
     * 下载数据报告
     */
    public function getDownloadFile($taskId){
        $url = $this->getUrl('/api/report/downloadFile');
        $csvContent =  $this->authRequestFile($url, [
            'taskId' => $taskId
        ], 'POST');

        return  iconv("gb2312",'utf-8',$csvContent);
    }


    /**
     * @param $param
     * @return mixed
     * 获取账户报告任务
     */
    public function getAccountReportTask($param){
        $url = $this->getUrl('/api/report/account');
        $reqParam = [
            'startDate' => $param['date'],
            'endDate' => $param['date'],
            'unitOfTime' => $param['unit_of_time'],
            'performanceData' => ['srch','click','consume','bindingConversion']
        ];
        return $this->authRequest($url, $reqParam, 'POST');
    }


    /**
     * @param $taskId
     * @return array
     * 获取账户维度数据报告任务数据
     */
    public function getAccountTaskData($taskId){
        $csvContent = $this->getDownloadFile($taskId);
        $list = explode("\r\n",$csvContent);
        $fieldName =  explode(',',$list[0]);
        unset($list[0]);
        $data = [];
        foreach ($list as $item){
            if(empty($item)) continue;
            $tmp = explode(',',$item);
            // 字段名映射成下标
            $tmpItem = [];
            foreach ($fieldName as $k => $v){
                $tmpItem[$v] =$tmp[$k];
            }

            $data[] = [
                'date'              => $tmpItem['日期'],
                'time'              => $tmpItem['时间'],
                'account_id'        => $tmpItem['账户ID'],
                'srch'              => intval($tmpItem['展现数']),
                'click'             => intval($tmpItem['点击数']),
                'consume'           => $tmpItem['消费'],
                'binding_conversion'=> intval($tmpItem['转化数（回传时间）'])
            ];
        }
        return $data;
    }


    /**
     * @param $param
     * @return mixed
     * 获取创意报告任务
     */
    public function getCreativeReport($param){
        $url = $this->getUrl('/api/report/creative');
        $reqParam = [
            'startDate' => $param['date'],
            'endDate' => $param['date'],
            'unitOfTime' => $param['unit_of_time'],
            'performanceData' => ['srch','click','consume','bindingConversion']
        ];
        return $this->authRequest($url, $reqParam, 'POST');
    }



    /**
     * @param $taskId
     * @return array
     * 获取创意维度数据报告任务数据
     */
    public function getCreativeTaskData($taskId){
        $csvContent = $this->getDownloadFile($taskId);
        $list = explode("\r\n",$csvContent);
        $fieldName =  explode(',',$list[0]);
        unset($list[0]);
        $data = [];
        foreach ($list as $item){
            if(empty($item)) continue;
            $tmp = explode(',',$item);
            // 字段名映射成下标
            $tmpItem = [];
            foreach ($fieldName as $k => $v){
                $tmpItem[$v] =$tmp[$k];
            }

            $data[] = [
                'date'              => $tmpItem['日期'],
                'time'              => $tmpItem['时间'],
                'account_id'        => $tmpItem['账户ID'],
                'adgroup_id'        => $tmpItem['推广组ID'],
                'campaign_id'       => $tmpItem['推广计划ID'],
                'creative_id'       => $tmpItem['创意ID'],
                'srch'              => intval($tmpItem['展现数']),
                'click'             => intval($tmpItem['点击数']),
                'consume'           => $tmpItem['消费'],
                'binding_conversion'=> intval($tmpItem['转化数（回传时间）']),
            ];
        }
        return $data;
    }

}
