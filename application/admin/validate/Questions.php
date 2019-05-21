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
	    'id|课程信息' => 'require|number|isExist:course,id',
        'title|课程名称' => 'require|max:25',
        'code|课程代码' => 'require|number|max:7',
        'classify_id|课程分类' =>'require|isExist:classify,id',
        'mold_id|考题类型' =>'require|array'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => '课程信息有误',
        'id.number' => '课程信息有误',
        'id.isExist' => '课程信息有误',
        'classify_id.isExist' => '课程分类信息有误',
        'mold_id.isExist' => '考题类型信息有误',
    ];

    /*
     * 定义验证场景
     * 格式：'场景名' => ['字段名1','字段名2']
     * */
    protected $scene = [
        'save' => ['title','code','classify_id','mold_id'],
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
