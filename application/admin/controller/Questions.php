<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use think\Request;
use app\admin\model\Questions as QuestionsM;
use app\admin\validate\Questions as QuestionsV;
use app\admin\model\Course;
use app\admin\model\Mold;
use app\admin\model\Choices;
use think\Db;


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
            ->append(['course_name','mold_name','status_text','sub_type'])
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
        $course = Course::field('id,code,title')->select();
        $mold = Mold::all();
        $this->assign('Course',$course);
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
        /**
         * 单选或多选时，选项信息验证
         */
        if($data['mold_id'] == 1 || $data['mold_id'] ==2){
            if(!isset($data['choice']) || !is_array($data['choice'])){
                return $this->failJson('答案选项信息有误');
            }
            foreach($data['choice'] as $val){
                if(empty($val))
                    return $this->failJson('答案选项信息有误');
            }
        }
        /**
         * 单选、多选、判断时，正确答案信息验证
         */
        if($data['mold_id'] == 1 || $data['mold_id'] ==3){  //单选、判断
            if(!isset($data['correct']) || empty($data['correct'])){
                return $this->failJson('正确答案信息有误');
            }
        }else if($data['mold_id'] == 2){    //多选
            if(!isset($data['correct']) || !is_array($data['correct'])){
                return $this->failJson('正确答案信息有误');
            }
            foreach($data['correct'] as $val){
                if(empty($val))
                    return $this->failJson('正确答案信息有误');
            }
            $data['correct']= implode(',',$data['correct']);
        }

        //事务提交订单
        Db::startTrans();
        try{

            $questions = QuestionsM::create($data);
            if($data['mold_id'] == 1 || $data['mold_id'] == 2){
                foreach($data['choice'] as $key=>$value){
                    $choiceData[$key]['questions_id'] = $questions->id;
                    $choiceData[$key]['opts'] = $value;

                }
                $choice = new Choices();
                $choice->saveAll($choiceData);
            }

            // 提交事务
            Db::commit();
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            return $this->failJson('添加失败');
        }

        return $this->successJson('新增成功','/aoogi/questions');
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
        $course = Course::field('id,code,title')->select();
        $mold = Mold::all();
        $this->assign('Course',$course);
        $this->assign('Mold',$mold);
        $questions['correct'] = explode(',',$questions['correct']);
        if(in_array($questions['mold_id'],[1,2])){
            $choices = Choices::field('id,opts')->where('questions_id',$questions['id'])->order('id asc')->select();
            $this->assign('Choices',$choices);
        }
        $this->assign('Questions',$questions);

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
