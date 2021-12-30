<?php

namespace App\Http\Controllers\Admin;

use App\Common\Controllers\Admin\AdminController;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Models\ClickModel;
use App\Common\Tools\CustomException;
use App\Services\AdvConvertCallbackService;
use Illuminate\Http\Request;

class ClickController extends AdminController
{
    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'click_at';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ClickModel();

        parent::__construct();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                // 2小时内
                $datetime = date('Y-m-d H:i:s', strtotime("-2 hours"));
                $builder->where('click_at', '>', $datetime);
            });
        });
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 回传
     */
    public function callback(Request $request){

        $this->validRule($request->post(), [
            'event_type' => 'required',
            'subject_type' => 'required',
        ]);

        $eventType = $request->post('event_type');
        $subjectType = $request->post('subject_type');

        $advConvertCallbackService = new AdvConvertCallbackService();
        $eventTypeMap = $advConvertCallbackService->getEventTypeMap($subjectType);
        $eventTypes = array_values($eventTypeMap);
        if(!in_array($eventType, $eventTypes)){
            throw new CustomException([
                'code' => 'UNKNOWN_EVENT_TYPE',
                'message' => '非合法回传类型',
            ]);
        }

        if($subjectType == ProductTypeEnums::KYY){
            $this->validRule($request->post(), [
                'muid' => 'required'
            ]);
            $muid = md5(trim($request->post('muid')));

            // 2小时内
            $datetime = date('Y-m-d H:i:s', strtotime("-2 hours"));
            $click = (new ClickModel())
                ->where('click_at', '>', $datetime)
                ->orderBy('click_at','desc')
                ->where('muid',$muid)
                ->first();
            if(empty($click)){
                throw new CustomException([
                    'code' => 'NOT_FOUND_CLICK',
                    'message' => '找不到对应点击',
                ]);
            }
        }else{

            throw new CustomException([
                'code' => 'NOT_SUPPORTED',
                'message' => '不支持该类型回传',
            ]);

        }

        $ret = $advConvertCallbackService->runCallback($click, $eventType,time());

        return $this->ret($ret);
    }
}
