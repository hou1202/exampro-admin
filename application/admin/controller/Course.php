<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Course as CourseM;
use app\admin\validate\Course as CourseV;
use app\admin\model\Classify;
use app\admin\model\Mold;


class Course extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('course/index');
    }

    //获取列表数据
    public function getData(Request $request)
    {
        $data = $request -> post();
        $map[] = ['id','>',0];
        if(isset($data['keyword']) && !empty($data['keyword'])){
            $map[] = ['id|title|code','like','%'.trim($data['keyword']).'%'];
        }
        $list = CourseM::where($map)
            ->append(['classify_name'])
            ->limit(($data['page']-1)*$data['limit'],$data['limit'])
            ->select();
        $count = CourseM::where($map)->count('id');
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

        $course = CourseM::where('id',$id)->append(['class_name'])->find();
        if(!$course) return $this->redirectError('非有效数据信息');
        $mold = Mold::all();
        $course['mold_id'] = explode('-',$course['mold_id']);
        $this->assign('Mold',$mold);
        $this->assign('Course',$course);
        return view('course/read');
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
        return view('course/create');
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
        $validate = new CourseV();
        if(!$validate->scene('save')->check($data))
            return $this->failJson($validate->getError());
        $data['mold_id'] = implode('-',$data['mold_id']);
        return CourseM::create($data) ? $this->successJson('新增成功','/aoogi/course') : $this->failJson('添加失败');
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

        $course = CourseM::get($id);
        if(!$course) return $this->redirectError('非有效数据信息');
        $classify = Classify::field('id,title')->where('pid',0)->select();
        $mold = Mold::all();
        $this->assign('Classify',$classify);
        $this->assign('Mold',$mold);
        $this->assign('Course',$course);
        $course['mold_id'] = explode('-',$course['mold_id']);
        return view('course/edit');
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
        $course = CourseM::get($id);
        if(!$course) return $this->failJson('非有效数据信息');
        $data = $request -> param();
        $validate = new CourseV();
        if(!$validate->sceneEdit()->check($data))
            return $this->failJson($validate->getError());
        $repeatCourse = CourseM::where('code',$data['code'])->where('id','<>',$id)->find();
        if($repeatCourse)
            return $this->failJson('课程代码已存在');
        $data['mold_id'] = implode('-',$data['mold_id']);
        return $course->save($data) ? $this->successJson('更新成功','/aoogi/course') : $this->failJson('更新失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        return CourseM::destroy($id) ? $this->successJson('删除成功') : $this->failJson('删除失败');

    }
}
