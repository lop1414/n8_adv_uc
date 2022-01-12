<?php
namespace App\Http\Controllers\Admin\Uc;


use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Models\Uc\UcAccountModel;
use App\Services\Uc\UcAccountService;
use Illuminate\Http\Request;

class AccountController extends UcController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UcAccountModel();

        parent::__construct();
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            $map = $this->getAdminUserMap();
            foreach ($this->curdService->responseData['list'] as $item){
                $item->admin_name = $item->admin_id ? $map[$item->admin_id]['name'] : '';
                $item->manageAccount;
            }
        });
    }


    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->admin_name =  $this->adminMap[$item->admin_id]['name'];
                $item->manageAccount;
            }
        });
    }


    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $adminId = $this->curdService->responseData->admin_id;
            $map = $this->getAdminUserMap([
                'id'  => $adminId
            ]);
            $this->curdService->responseData->admin_name = $map[$adminId]['name'];
            $this->curdService->responseData->manageAccount;
        });
    }




    /**
     * 保存验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('account_id')->addValidRule('required');
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('rebate')->addValidRule('required');
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();
        $this->curdService->addColumns(['account_id']);
        $this->curdService->saveBefore(function(){
            $this->curdService->handleData['parent_id'] = 0;
            $this->curdService->handleData['admin_id'] = 0;
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){

            if(
                $this->curdService->getModel()->account_id != $this->curdService->handleData['account_id']
                && $this->curdService->getModel()->exist('account_id', $this->curdService->handleData['account_id'])
            ){
                throw new CustomException([
                    'code' => 'ACCOUNT_EXIST',
                    'message' => '账户已存在'
                ]);
            }
            unset($this->curdService->handleData['parent_id']);


        });
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 同步管家下的子账户
     */
    public function syncAccount(Request $request){
        $requestData = $request->all();

        $this->curdService->setRequestData($requestData);

        // 查找
        $item = $this->curdService->read();

        if($item['parent_id'] != 0){
            throw new CustomException([
                'code' => 'NOT_MANAGE_ACCOUNT',
                'message' => '不是管家账户'
            ]);
        }

        (new UcAccountService($item))->syncSubAccount();


        return $this->success();

    }


    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 批量更新管理员
     */
    public function batchUpdateAdmin(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'admin_id'    =>  'required',
            'account_ids' =>  'required'
        ]);

        foreach ($req['account_ids'] as $accountId){
            $account = (new UcAccountModel())
                ->where('account_id',$accountId)
                ->first();
            if(empty($account)) continue;
            $account->admin_id = $req['admin_id'];
            $account->save();
        }

        return $this->success();
    }
}
