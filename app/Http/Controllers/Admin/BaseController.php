<?php


namespace App\Http\Controllers\Admin;


use App\Common\Controllers\Admin\AdminController;
use App\Common\Helpers\Functions;
use App\Common\Services\SystemApi\CenterApiService;


class BaseController extends AdminController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'created_at';


    public $adminUser;


    /**
     * constructor.
     */
    public function __construct()
    {

        parent::__construct();
        $this->adminUser = Functions::getGlobalData('admin_user_info');

    }


    public function getAdminUserMap($filter = []){
        $adminUsers = (new CenterApiService())->apiGetAdminUsers($filter);
        $tmp = array_column($adminUsers,null,'id');
        // 兼容没有admin_id
        $tmp[0] = ['name' => ''];
        return $tmp;
    }


    /**
     * 有数据权限
     * @return bool
     */
    public function isDataAuth(){
        if($this->adminUser['is_admin']) return true;

        return false;
    }

}
