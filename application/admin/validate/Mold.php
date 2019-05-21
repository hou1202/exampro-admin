<?php

namespace app\admin\validate;

use app\common\validate\CommonValidate;


class Mold extends CommonValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'id|类型ID' => 'require|number|isExist:mold,id',
        'title|类型名称' => 'require|max:10',
        'code|类型代码' => 'require|number|integer|gt:0',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => '类型信息有误',
        'id.number' => '类型信息有误',
        'id.isExist' => '类型信息有误',
    ];

    /*
     * 定义验证场景
     * 格式：'场景名' => ['字段名1','字段名2']
     * */
    protected $scene = [
        'save' => ['code','title'],
        'edit' => ['id','code','title'],
    ];


}
