<?php


namespace App\Http\Controllers\Admin\Uc;


use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Enums\Uc\UcSyncTypeEnum;
use App\Http\Controllers\Admin\BaseController;
use App\Models\BaiDu\BaiDuAccountModel;
use App\Sdks\BaiDu\Traits\Request;
use App\Services\Task\TaskBaiDuSyncService;


class UcController extends BaseController
{

    protected $adminMap;

    public function __construct(){
        parent::__construct();
        $this->adminMap = $this->getAdminUserMap();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                $builder->withPermission();

                // 筛选管理员
                $adminId = $this->curdService->requestData['admin_id'] ?? '';
                if($adminId){
                    $builder->whereRaw("account_id IN (
                        SELECT account_id FROM uc_accounts
                            WHERE admin_id = {$adminId}
                    )");
                }
            });
        });
    }

    /**
     * 查询（无分页）预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function(){
            $this->curdService->customBuilder(function($builder){
                $builder->withPermission();
            });
        });
    }




    public function syncBefore(){}

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 同步
     */
    public function sync(Request $request){
        $this->validRule($request->post(), [
            'account_ids' => 'required|array',
            'type' => 'required',
        ]);

        $accountIds = $request->post('account_ids');
        $syncType = $request->post('type');

        Functions::hasEnum(UcSyncTypeEnum::class, $syncType);

        // 获取后台用户信息
        $adminUserInfo = Functions::getGlobalData('admin_user_info');

        // 获取账户
        $accountModel = new BaiDuAccountModel();
        $builder = $accountModel->whereIn('account_id', $accountIds);

        // 非管理员
        if(!$adminUserInfo['is_admin']){
            $builder->where('admin_id', $adminUserInfo['admin_user']['id']);
        }

        $accounts = $builder->get();
        if(!$accounts->count()){
            throw new CustomException([
                'code' => 'NOT_FOUND_ACCOUNT',
                'message' => '找不到对应账户',
            ]);
        }

        $this->syncBefore();

        // 创建任务
        $taskBaiDuSyncService = new TaskBaiDuSyncService($syncType);
        $syncTypeName = Functions::getEnumMapName(BaiDuSyncTypeEnum::class, $syncType);
        $task = [
            'name' => "百度{$syncTypeName}同步",
            'admin_id' => $adminUserInfo['admin_user']['id'],
        ];

        $subs = [];
        foreach($accounts as $account){
            $subs[] = [
                'app_id' => $account->app_id,
                'account_id' => $account->account_id,
                'admin_id' => $adminUserInfo['admin_user']['id'],
            ];
        }
        $taskBaiDuSyncService->create($task, $subs);

        $this->syncAfter();

        return $this->success([
            'task_id' => $taskBaiDuSyncService->taskId,
            'account_count' => $accounts->count(),
        ], [], "批量同步{$syncTypeName}任务已提交【任务id:{$taskBaiDuSyncService->taskId}】，执行结果后续同步到飞书，请注意查收！");
    }

    public function syncAfter(){}



}
