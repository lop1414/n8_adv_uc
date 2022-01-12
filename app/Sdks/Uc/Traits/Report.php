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
        unset($list[0]);
        $data = [];
        foreach ($list as $item){
            if(empty($item)) continue;
            $tmp = explode(',',$item);

            $data[] = [
                'date'              => $tmp[0],
                'time'              => $tmp[1],
                'account_id'        => $tmp[3],
                'srch'              => $tmp[4],
                'click'             => $tmp[5],
                'consume'           => $tmp[6],
                'binding_conversion'=> $tmp[7],
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
        unset($list[0]);
        $data = [];
        foreach ($list as $item){
            if(empty($item)) continue;
            $tmp = explode(',',$item);
            $data[] = [
                'date'              => $tmp[0],
                'time'              => $tmp[1],
                'account_id'        => $tmp[2],
                'adgroup_id'        => $tmp[4],
                'campaign_id'       => $tmp[6],
                'creative_id'       => $tmp[8],
                'srch'              => $tmp[14],
                'click'             => $tmp[15],
                'consume'           => $tmp[16],
                'binding_conversion'=> $tmp[17],
            ];
        }
        return $data;
    }

}
