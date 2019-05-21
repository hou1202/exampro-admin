<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Mold as MoldM;
use app\admin\validate\Mold as MoldV;


class Mold extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('mold/index');
    }

    //获取列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title|code','like','%'.trim($data['keyword']).'%'];
        }
        $list = MoldM::where($map)
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = MoldM::where($map)->count('id');
        return $this->kitJson($list,$count);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('mold/create');
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
        $validate = new MoldV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());
        if(MoldM::where('code',$data['code'])->find())
            return $this->failJson('类型代码已存在');
        return MoldM::create($data) ? $this->successJson('新增成功','/aoogi/mold') : $this->failJson('添加失败');
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $mold = MoldM::get($id);
        if(!$mold) return $this->redirectError('非有效数据信息');
        $this->assign('Mold',$mold);
        return view('mold/edit');
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
        $mold = MoldM::get($id);
        if(!$mold) return $this->failJson('非有效数据信息');
        $data = $request -> param();
        $validate = new MoldV();
        if(!$validate->scene('edit')->check($data))
            return $this->failJson($validate->getError());
        if(MoldM::where('code',$data['code'])->where('id',"<>",$id)->find())
            return $this->failJson('类型代码已存在');
        return $mold->save($data) ? $this->successJson('更新成功','/aoogi/mold') : $this->failJson('更新失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return MoldM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');
    }
}
