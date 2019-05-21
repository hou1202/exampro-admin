<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Questions as QuestionsM;
use app\admin\validate\Questions as QuestionsV;
use app\admin\model\Classify;
use app\admin\model\Mold;


class Questions extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('questions/index');
    }

    //获取列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title|code','like','%'.trim($data['keyword']).'%'];
        }
        $list = QuestionsM::where($map)
            ->append(['classify_name'])
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = QuestionsM::where($map)->count('id');
        return $this->kitJson($list,$count);
    }

    /**
     * 显示资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {

        $questions = QuestionsM::where('id',$id)->append(['class_name'])->find();
        if(!$questions) return $this->redirectError('非有效数据信息');
        $mold = Mold::all();
        $questions['mold_id'] = explode('-',$questions['mold_id']);
        $this->assign('Mold',$mold);
        $this->assign('Questions',$questions);
        return view('questions/read');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $classify = Classify::field('id,title')->where('pid',0)->select();
        $mold = Mold::all();
        $this->assign('Classify',$classify);
        $this->assign('Mold',$mold);
        return view('questions/create');
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
        $validate = new QuestionsV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());
        $data['mold_id'] = implode('-',$data['mold_id']);
        return QuestionsM::create($data) ? $this->successJson('新增成功','/aoogi/questions') : $this->failJson('添加失败');
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

        $questions = QuestionsM::get($id);
        if(!$questions) return $this->redirectError('非有效数据信息');
        $classify = Classify::field('id,title')->where('pid',0)->select();
        $mold = Mold::all();
        $this->assign('Classify',$classify);
        $this->assign('Mold',$mold);
        $this->assign('Questions',$questions);
        $questions['mold_id'] = explode('-',$questions['mold_id']);
        return view('questions/edit');
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
        $questions = QuestionsM::get($id);
        if(!$questions) return $this->failJson('非有效数据信息');
        $data = $request -> param();
        $validate = new QuestionsV();
        if(!$validate->sceneEdit()->check($data))
            return $this->failJson($validate->getError());
        $repeatQuestions = QuestionsM::where('code',$data['code'])->where('id','<>',$id)->find();
        if($repeatQuestions)
            return $this->failJson('课程代码已存在');
        $data['mold_id'] = implode('-',$data['mold_id']);
        return $questions->save($data) ? $this->successJson('更新成功','/aoogi/questions') : $this->failJson('更新失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        return QuestionsM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');

    }
}
