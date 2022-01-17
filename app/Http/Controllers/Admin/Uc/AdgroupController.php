<?php
namespace App\Http\Controllers\Admin\Uc;



use App\Models\Uc\UcAdgroupModel;

class AdgroupController extends UcController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UcAdgroupModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        parent::selectPrepare();
        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
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
