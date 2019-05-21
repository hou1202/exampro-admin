<?php

namespace app\admin\validate;

use app\common\validate\CommonValidate;


class Classify extends CommonValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'id|分类ID' => 'require|number|isExist:classify,id',
        'title|分类名称' => 'require|max:25',
        'pid|父级分类' => 'require|number|integer',
        'thumbnail|缩略图' =>'require|max:255'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => '分类信息有误',
        'id.number' => '分类信息有误',
        'id.isExist' => '分类信息有误',
    ];

    /*
     * 定义验证场景
     * 格式：'场景名' => ['字段名1','字段名2']
     * */
    protected $scene = [
        'save' => ['title','pid','thumbnail'],
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
