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
        return RouteM::create($data) ? $this->successJson('新增成功','/aoogi/router') : $this->failJson('添加失败');
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
        $pid = $data['pid'];
        if(isset($data['type']['site'])){
            if($pResource != 0){
                return $this->failJson('父级路由为二级时，不可存在“设置”项');
            }
            $siteResp['pid'] =  $pid;
            $siteResp['router'] = '/aoogi/'.$data['modular'];
            $siteResp['menu'] = '/aoogi/'.$data['modular'];
            $siteResp['path'] = 'admin/'.$data['modular'].'/index';
            $siteResp['title'] = $data['title'].'设置';
            $siteResp['icon'] = '#xe653;';
            $siteResp['open'] = 0;
            $siteResp['main'] = 1;
            $siteResp['status'] = 1;
            $siteResp['opts'] = 1;
            if($res = RouteM::create($siteResp)){
                $pid = $res->id;
            }else{
                return $this->failJson('添加失败');
            }
        }
        if(isset($data['type']['list'])){
            $resp[1]['router'] = '/aoogi/'.$data['modular'].'/data';
            $resp[1]['menu'] = '/aoogi/'.$data['modular'].'/data';
            $resp[1]['path'] = 'admin/'.$data['modular'].'/getData';
            $resp[1]['title'] = $data['title'].'列表';
            $resp[1]['pid'] =  $pid;
            $resp[1]['status'] = 1;
        }
        if(isset($data['type']['status'])){
            $resp[2]['router'] = '/aoogi/'.$data['modular'].'/status';
            $resp[2]['menu'] = '/aoogi/'.$data['modular'].'/status';
            $resp[2]['path'] = 'admin/'.$data['modular'].'/setStatus';
            $resp[2]['title'] = $data['title'].'状态';
            $resp[2]['pid'] =  $pid;
            $resp[2]['status'] = 1;
            $resp[2]['opts'] = 0;
        }
        if(isset($data['type']['create'])){
            $resp[3]['router'] = '/aoogi/'.$data['modular'].'/create';
            $resp[3]['menu'] = '/aoogi/'.$data['modular'].'/create';
            $resp[3]['path'] = 'admin/'.$data['modular'].'/create';
            $resp[3]['title'] = '新增'.$data['title'];
            $resp[3]['pid'] =  $pid;
            $resp[3]['status'] = 1;
            $resp[3]['opts'] = 1;

            $resp[4]['router'] = '/aoogi/'.$data['modular'];
            $resp[4]['menu'] = '/aoogi/'.$data['modular'].'/save';
            $resp[4]['path'] = 'admin/'.$data['modular'].'/save';
            $resp[4]['title'] = '保存'.$data['title'];
            $resp[4]['pid'] =  $pid;
            $resp[4]['status'] = 1;
            $resp[4]['opts'] = 0;
        }
        if(isset($data['type']['edit'])){
            $resp[5]['router'] = '/aoogi/'.$data['modular'].'/edit/:id';
            $resp[5]['menu'] = '/aoogi/'.$data['modular'].'/edit/*';
            $resp[5]['path'] = 'admin/'.$data['modular'].'/edit';
            $resp[5]['title'] = '编辑'.$data['title'];
            $resp[5]['pid'] =  $pid;
            $resp[5]['status'] = 1;
            $resp[5]['opts'] = 1;

            $resp[6]['router'] = '/aoogi/'.$data['modular'].'/:id';
            $resp[6]['menu'] = '/aoogi/'.$data['modular'].'/update/*';
            $resp[6]['path'] = 'admin/'.$data['modular'].'/update';
            $resp[6]['title'] = '更新'.$data['title'];
            $resp[6]['pid'] =  $pid;
            $resp[6]['status'] = 1;
            $resp[6]['opts'] = 0;
        }
        if(isset($data['type']['del'])){
            $resp[7]['router'] = '/aoogi/'.$data['modular'].'/:id';
            $resp[7]['menu'] = '/aoogi/'.$data['modular'].'/del/*';
            $resp[7]['path'] = 'admin/'.$data['modular'].'/delete';
            $resp[7]['title'] = '删除'.$data['title'];
            $resp[7]['pid'] =  $pid;
            $resp[7]['status'] = 1;
            $resp[7]['opts'] = 0;
        }
        if(isset($data['type']['read'])){
            $resp[8]['router'] = '/aoogi/'.$data['modular'].'/:id';
            $resp[8]['menu'] = '/aoogi/'.$data['modular'].'/read/*';
            $resp[8]['path'] = 'admin/'.$data['modular'].'/read';
            $resp[8]['title'] = '查看'.$data['title'];
            $resp[8]['pid'] =  $pid;
            $resp[8]['status'] = 1;
            $resp[8]['opts'] = 1;
        }


        //var_dump($resp);die;
        $routerM = new RouteM;
        return $routerM->saveAll($resp) ?$this->successJson('新增成功','/aoogi/router') : $this->failJson('添加失败');
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
