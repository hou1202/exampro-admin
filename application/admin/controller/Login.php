<?php

namespace app\admin\controller;

use app\admin\validate\Admin as AdminV;
use think\Controller;
use think\Request;
use app\admin\model\Admin as AdminM;
use app\admin\model\Permission;
use app\admin\common\User;

class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(User::check()) return redirect('/admin');
        return view('/login');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function login(Request $request)
    {
        $data = $request -> post();
        $validate = new AdminV();
        if(!$validate->sceneLogin()->check($data)){
            return json($validate->getError());
        }
        $map[] = ['account','=',$data['account']];
        $map[] = ['password','=',md5($data['password'])];
        $admin = AdminM::field('id,account,password,status,last_ip,last_at,count,permissions_id')
            ->where($map)
            ->find();
        if(!$admin) return json(['data' =>'帐户或密码信息有误']);
        if(!$admin['status']) return json(['data' =>'帐户已被禁用，请联系管理员']);
        if(!$per = Permission::where('id',$admin['permissions_id'])->where('status',1)->find()) return json(['data' =>'帐户权限组已被禁用']);
        if(!User::login($admin['account'])) return json(['data' =>'登录失败，请重新登录']);
        $admin->save([
            'last_ip' => $request->ip(),
            'last_at' => time(),
            'count'   => $admin['count']+1
        ]);

        $res = [
            'data' => '登录成功',
            'url' => '/admin'
        ];
        return json($res);

    }

}
