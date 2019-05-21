<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Classify as ClassifyM;
use app\admin\validate\Classify as ClassifyV;


class Classify extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('classify/index');
    }

    //获取列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title','like','%'.trim($data['keyword']).'%'];
        }
        $list = ClassifyM::where($map)
            ->append(['pid_name'])
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = ClassifyM::where($map)->count('id');
        return $this->kitJson($list,$count);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $main = ClassifyM::field('id,title')->where('pid',0)->select();
        $this->assign('main',$main);
        return view('classify/create');
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
        $validate = new ClassifyV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());
        return ClassifyM::create($data) ? $this->successJson('新增成功','/aoogi/classify') : $this->failJson('添加失败');
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $main = ClassifyM::field('id,title')->where('pid',0)->select();
        $classify = ClassifyM::get($id);
        if(!$classify) return $this->redirectError('非有效数据信息');
        $this->assign('main',$main);
        $this->assign('Classify',$classify);
        return view('classify/edit');
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
        $classify = ClassifyM::get($id);
        if(!$classify) return $this->failJson('非有效数据信息');
        $data = $request -> param();
        if($data['id'] == $data['id'])
            return $this->failJson('父级分类不能为自己');

        $validate = new ClassifyV();
        if(!$validate->sceneEdit()->check($data))
            return $this->failJson($validate->getError());
        return $classify->save($data) ? $this->successJson('更新成功','/aoogi/classify') : $this->failJson('更新失败');
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
        if(ClassifyM::where('pid',$id)->value('id'))
            return $this->failJson('请先删除该分类下子分类');
        return ClassifyM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');

    }
}
