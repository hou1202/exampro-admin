<?php

namespace app\admin\validate;

use app\common\validate\CommonValidate;


class Questions extends CommonValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'id|考题信息' => 'require|isExist:course,id',
        'course_id|课程名称' => 'require|isExist:course,id',
        'code|课程代码' => 'require|isExist:course,code',
        'mold_id|考题类型' =>'require|isExist:mold,id',
        'title|考题标题' => 'require|max:255|unique:questions,title',
        'analysis|答案解析' => 'require',
        'status|考题状态' => 'require|in:1,2,3',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => '考题信息有误',
        'id.isExist' => '考题信息有误',
        'course_id.require' => '课程名称信息有误',
        'course_id.isExist' => '课程名称信息有误',
        'code.require' => '课程代码信息有误',
        'code.isExist' => '课程代码信息有误',
        'mold_id.require' => '考题类型信息有误',
        'mold_id.isExist' => '考题类型信息有误',
        'title.unique' => '考题已存在',
        'status.in' => '考题状态有误'
    ];

    /*
     * 定义验证场景
     * 格式：'场景名' => ['字段名1','字段名2']
     * */
    protected $scene = [
        'save' => ['course_id','code','mold_id','title','analysis','status'],
        'edit' => ['id','title','code','classify_id','mold_id'],
    ];

    /*
     * 定义编辑 edit 模式下，验证场景
     * */
    public function sceneEdit()
    {
        return $this -> only(['id','title','pid',])
            ->remove('thumbnail','require');
    }
}
