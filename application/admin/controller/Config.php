<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Validate;
use think\Request;
use app\admin\model\Config as ConfigM;

class Config extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return view('config/index');
    }

    //获取列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title','like','%'.trim($data['keyword']).'%'];
        }
        $list = ConfigM::where($map)
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = ConfigM::where($map)->count('id');
        return $this->kitJson($list,$count);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return view('config/create');
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
        $rule = [
            'title|参数名称' => 'require|max:15',
            'param|参数值' => 'require',
        ];
        $validate = new Validate($rule);
        if(!$validate->check($data)){
            return $this->failJson($validate->getError());
        }
        return ConfigM::create($data) ? $this->successJson('新增成功','/aoogi/config') : $this->failJson('添加失败',0);
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
        $config = ConfigM::get($id);
        if(!$config) return $this->redirectError('非有效数据信息');
        $this->assign('Conf',$config);
        return view('config/edit');
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

        $data = $request -> post();
        $config = ConfigM::get($id);
        if(!$config) return $this->failJson('非有效数据信息');
        $rule = [
            'title|参数名称' => 'require|max:15',
            'param|参数值' => 'require',
        ];
        $validate = new Validate($rule);
        if(!$validate->check($data)){
            return $this->failJson($validate->getError());
        }
        return $config->save($data) ? $this->successJson('更新成功','/aoogi/config') : $this->failJson('更新失败');
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
        return ConfigM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');
    }
}
