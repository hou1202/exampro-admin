<?php

namespace app\admin\validate;

use app\common\validate\CommonValidate;


class Router extends CommonValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'id|路由ID' => 'require|number',
        'title|路由名称' => 'require|max:15',
        'router|系统路由' => '/(^\/)\w*/',
        'menu|菜单路由' => '/(^\/)\w*/',
        'path|系统路径' => '/(^[a-zA-Z])\w*/',
        'icon|路由图标' => '/(^#)[a-z0-9]{5}(;)$/',
        'pid|父级ID' => 'number|length:1,11',
        'open|是否展开' => 'in:0,1',
        'main|是否主目录' => 'in:0,1',
        'status|路由状态' =>'in:0,1',
        'opts|是否配置项路由' =>'in:0,1',
        'modular|模块名称' =>'/(^[a-zA-Z])\w*/',
        'type|模块类型' =>'require|array',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => '路由信息有误',
        'id.number' => '路由信息有误',
        'router' => '系统路由格式错误,例：/admin',
        'menu' => '菜单路由格式错误,例：/admin',
        'path' => '系统路径格式错误,例：/admin/index/index',
        'pid.number' => '父级ID有误！若为主目录别ID为0',
        'pid.length' => '父级ID有误！若为主目录别ID为0',
        'open.in' => '主目录是否展开信息有误',
        'main.in' => '是否为主目录信息有误',
        'status.in' => '路由状态信息有误',
        'opts.in' => '是否配置项路由信息有误',
        'icon' => '路由图标格式错误，例：#xe67a;',
        'modular' => '模块名称错误，例：admin;',
        'type' => '模块类型错误',
    ];

    /*
     * 定义验证场景
     * 格式：'场景名' => ['字段名1','字段名2']
     * */
    protected $scene = [
        'status' => ['id','status'],
        'save' => ['title','router','menu','path','icon','pid','open','main','status','opts'],
        'modular' => ['title','modular','type']
    ];
}
