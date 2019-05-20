<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Router;
use app\admin\model\Admin;
use app\admin\model\Permission as PermissionM;
use app\admin\validate\Permission as PermissionV;


class Permission extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return view('permission/index');
    }

    //获取数据列表
    public function getData(Request $request)
    {
        $data = $request -> post();
        $list = PermissionM::limit(($data['page']-1)*$data['limit'],$data['limit']) -> select();
        $count = PermissionM::count('id');

        return $this->kitJson($list,$count);
    }

    //设置状态
    public function setStatus(Request $request)
    {
        $data = $request -> post();
        $validate = new PermissionV();
        if(!$validate->scene('status')->check($data))
            return $this->failJson($validate->getError());

        $permission = PermissionM::get($data['id']);
        if(!$permission) return $this->failJson('非有效数据信息');
        return $permission->save($data) ? $this->successJson('状态更新成功') : $this->failJson('状态更新失败');
    }


    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $router = Router::where('status',1)->all();
        if(!$router) return $this->redirectError('暂无路由信息，请先创建路由');
        $this->assign("Route",$router);
        return view('permission/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
        $data = $request -> post();
        $validate = new PermissionV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());

        $data['router_id'] = implode('-',$data['router_id']);
        return PermissionM::create($data) ? $this->successJson('新增成功','/aoogi/permission') : $this->failJson('添加失败');
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
        $permission = PermissionM::get($id);
        if(!$permission) return $this->redirectError('非有效数据信息');
        $router = Router::where('status',1)->all();
        $permission['router_id'] = explode('-',$permission['router_id']);
        $this->assign("Route",$router);
        $this->assign("Per",$permission);
        return view('permission/read');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $permission = PermissionM::get($id);
        if(!$permission) return $this->redirectError('非有效数据信息');
        $router = Router::where('status',1)->all();
        $permission['router_id'] = explode('-',$permission['router_id']);
        $this->assign("Route",$router);
        $this->assign("Per",$permission);
        return view('permission/edit');
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
        $permission = PermissionM::get($id);
        if(!$permission) return $this->failJson('非有效数据信息');

        $data = $request -> post();
        $validate = new PermissionV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());

        $data['router_id'] = implode('-',$data['router_id']);
        return $permission->save($data) ? $this->successJson('更新成功','/aoogi/permission') : $this->failJson('更新失败');
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
        if(Admin::where('permissions_id',$id)->find())
            return $this->failJson('该权限组正在被管理员使用中，请先调整管理员权限组');

        return PermissionM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');
    }
}
