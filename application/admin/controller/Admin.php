<?php

namespace app\admin\controller;

use app\admin\common\User;
use app\admin\model\Permission;
use app\common\controller\AdminController;
use think\Request;
use think\facade\Config;
use app\admin\model\Admin as AdminM;
use app\admin\validate\Admin as AdminValidate;

class Admin extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('admin/index');
    }

    /*
     * 获取资源信息
     *
     * @return json
     * */
    public function getData()
    {
        $data = AdminM::table('adminer')
            ->alias('a')
            ->field('a.id,a.account,a.name,a.status,a.last_ip,a.last_at,a.count,a.remark,p.title as permissions_id')
            ->join('permissions p','a.permissions_id = p.id')
            ->group('a.id')
            ->select();

        return $this->kitJson($data,count($data));
    }

    /*
     * @ setStatus     更新状态
     * @Method      POST
     * */
    public function setStatus(Request $request)
    {
        $data = $request -> post();
        $validate = new AdminValidate();
        if(!$validate->scene('status')->check($data))
            return $this->failJson($validate->getError());

        $adminer = AdminM::get($data['id']);
        if(!$adminer) return $this->failJson('非有效数据信息');
        if($adminer['account'] == Config::get('default_admin')) return $this ->failJson('该帐户状态无权限禁用');
        if($adminer['account'] == User::logined_uuid()) return $this ->failJson('无法禁用自己的帐户状态');
        return $adminer->save($data) ? $this ->successJson('状态更新成功') : $this ->failJson('状态更新失败');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $permission = Permission::field('id,title')->all();
        $this->assign('Per',$permission);
        return view('admin/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request -> post();
        $validate = new AdminValidate();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());

        return AdminM::create($data) ? $this ->successJson('新增成功','/aoogi/adminer') : $this ->failJson('添加失败');

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
        $adminer = AdminM::table('adminer')
            ->alias('a')
            ->field('a.*,p.title as per_id')
            ->join('permissions p','p.id = a.permissions_id')
            ->where('a.id',$id)
            ->find();
        if(!$adminer) return $this->redirectError('非有效数据信息');
        $this -> assign('Adminer',$adminer);
        return view('admin/read');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $adminer = AdminM::get($id);
        if(!$adminer) return $this->redirectError('非有效数据信息');
        $permission = Permission::field('id,title')->all();
        $this->assign('Per',$permission);
        $this->assign('Adminer',$adminer);
        return view('admin/edit');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
        $adminer = AdminM::get($id);
        if(!$adminer) return $this->failJson('非有效数据信息');
        $data = $request -> post();
        if(empty($data['password'])) unset($data['password']);

        $validate = new AdminValidate();
        if(!$validate->sceneEdit()->check($data))
            return $this->failJson($validate->getError());

        return $adminer -> save($data) ? $this ->successJson('更新成功','/aoogi/adminer') : $this ->failJson('更新失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        $adminer = AdminM::get($id);
        if(!$adminer) return $this->failJson('非有效数据信息');
        if($adminer['account'] == Config::get('default_admin')) return $this ->failJson('该帐户无权限删除');
        if($adminer['account'] == User::logined_uuid()) return $this ->failJson('无法删除自己的帐户');
        return $adminer->delete() ? $this->successJson('删除成功') : $this->failJson('删除失败');
    }
}
