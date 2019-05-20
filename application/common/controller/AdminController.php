<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/10/19
 * Time: 9:55
 */

namespace app\common\controller;


use think\Controller;

class AdminController extends BaseController
{
    //管理员资料
    protected $admin_info;

    //管理员权限
    protected $permission;

    //控制器验证中间键
    protected $middleware = ['Authority'];

    /*
     * init     初始化操作
     * */
    protected function initialize()
    {
        //var_dump($this->request);

    }


    /*
     * 返回kit_admin列表数据返回JSON格式的数据
     * @param       $data       数据源
     * @param       $count      数据总数量
     * @param       $code
     * @return      json
     * */
    protected function kitJson($data,$count,$code=0)
    {
        $res = [
            'code' => $code,
            'count' => $count,
            'data' => $data,
        ];
        return json($res);
    }

    /*
     * 返回kit_admin列表数据返回JSON格式的数据
     * @param  string     $data       数据源
     * @param  string     $url        跳转路由
     * @param  number     $code       1=》成功；0=》失败
     * @return      json
     * */
    protected function returnJson($data,$code=1,$url='')
    {
        $res = [
            'code' => $code,
            'url' => $url,
            'data' => $data,
        ];
        return json($res);
    }

    /*
     * 错误信息重定向
     * @param  string     $msg       提示信息
     * @param  string     $url       跳转路由
     * @return      redirect
     * */
    protected function redirectError($msg='抱歉，你暂无权限进行该操作.',$url='/error')
    {
        return $this->redirect($url,['msg'=>$msg]);
    }


}