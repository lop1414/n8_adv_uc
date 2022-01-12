<?php

namespace App\Http\Controllers;



use App\Services\Uc\Report\UcAccountReportService;
use App\Services\Uc\Report\UcCreativeReportService;
use Illuminate\Http\Request;

class TestController extends Controller
{



    public function test(Request $request){
        $key = $request->input('key');
        if($key != 'aut'){
            return $this->forbidden();
        }

//        $service = new UcAccountReportService();
//        $service->sync(['date' => '2022-01-11']);
        $service = new UcCreativeReportService();
        $service->sync(['date' => '2022-01-11']);

    }
}
