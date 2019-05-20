<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Router as RouteM;
use app\admin\validate\Router as RouterV;

class Router extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('router/index');
    }

    //获取路由列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title|router|path|menu','like','%'.trim($data['keyword']).'%'];
        }
        $list = RouteM::where($map)
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = RouteM::where($map)->count('id');
        return $this->kitJson($list,$count);
    }

    //设置状态
    public function setStatus(Request $request)
    {
        $data = $request -> post();
        $validate = new RouterV();
        if(!$validate->scene('status')->check($data))
            return $this->failJson($validate->getError());

        $route = RouteM::get($data['id']);
        if(!$route) return $this->failJson('非有效数据信息');
        return $route->save($data) ? $this->successJson('状态更新成功') : $this->failJson('状态更新失败');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $main = RouteM::field('id,title')->where('main',1)->where('status',1)->select();
        $this->assign('main',$main);
        return view('router/create');
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
        $validate = new RouterV();
        if(!$validate->scene('save')->check($data)){
            return $this->failJson($validate->getError());
        }
        return RouteM::create($data) ?$this->successJson('新增成功','/aoogi/router') : $this->failJson('添加失败');
    }

    /**
     * 显示模块化创建资源表单页.
     *
     * @return \think\Response
     */
    public function createModular()
    {
        $main = RouteM::field('id,title')->where('main',1)->where('status',1)->select();
        $this->assign('main',$main);
        return view('router/create_modular');
    }

    /**                                  
     * 保存新建的模块化资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function saveModular(Request $request)
    {
        //
        $data = $request -> post();

        $validate = new RouterV();
        if(!$validate->scene('modular')->check($data)){
            return $this->returnJson($validate->getError());
        }
        //var_dump($data);die;
        $pResource = RouteM::where('id',$data['pid'])->value('pid');

        $resp = array();
        if(isset($data['type']['site']) ){
            var_dump(1);die;
        }
        var_dump($data);die;
        return RouteM::create($data) ?$this->successJson('新增成功','/aoogi/router') : $this->failJson('添加失败');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {

        $route = RouteM::get($id);
        if(!$route) return $this->redirectError('非有效数据信息');
        $this->assign('Route',$route);
        return view('router/read');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $main = RouteM::field('id,title')->where('main',1)->where('status',1)->select();
        $route = RouteM::get($id);
        if(!$route) return $this->redirectError('非有效数据信息');
        $this->assign('main',$main);
        $this->assign('Route',$route);
        return view('router/edit');
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
        $route = RouteM::get($id);
        if(!$route) return $this->failJson('非有效数据信息');
        $data = $request -> param();
        $validate = new RouterV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());
        return $route->save($data) ? $this->successJson('更新成功','/aoogi/router') : $this->failJson('更新失败');
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
        if(RouteM::where('pid',$id)->find())
            return $this->failJson('该路由为主路由，若要删除请先删除所属子路由');
        return RouteM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');

    }
}
