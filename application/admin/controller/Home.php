<?php

/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/10/19
 * Time: 9:54
 */

namespace app\admin\controller;

use app\admin\common\Auth;
use app\admin\model\Router;
use app\common\controller\AdminController;

use think\facade\Config;
use think\facade\Request;
use app\admin\common\User;


class Home extends AdminController
{

    //主体框架加载信息
    public function home()
    {
        if(!$admin = User::user()) return redirect(Request::domain().'/adminLogin');
        $info = \app\admin\model\Config::field('param')->where('id','in',[1,2])->select();
        $this->assign('Info',$info);
        $this->assign('User',$admin);
        return $this->fetch('/index');
    }

    public function main()
    {
        return view('/main');
    }

    //退出登录状态
    public function logout()
    {
        User::logout();
        Auth::authOut();
        return $this ->successJson('退出成功',Request::domain().'/adminLogin');

    }

    //左右导航菜单目录
    public function menu(){
        $user = User::user();
        //系统默认管理员获取全部菜单
        if($user['account'] === Config::get('default_admin')){
            $res = Router::where('status',1)->where('main',1)->all();
        }else{
            $roles = Auth::getGroups();
            $res = Router::where('id','in',$roles)->where('status',1)->where('main',1)->select();
        };
        $xml = array();
        foreach($res as $router){
            if($router['pid'] == 0){
                $children = array();
                foreach ($res as $CRoute){
                    if($CRoute['pid'] == $router['id'] && $CRoute['main'] == 1){
                        $children[] = [
                            'id'=>$CRoute['id'],
                            'title'=>$CRoute['title'],
                            'path'=>'#'.$CRoute['menu'],
                            'icon'=>'&'.$CRoute['icon'],
                            'pid'=>$CRoute['pid'],
                            'open'=>false
                        ];
                    }
                }
                $xml[] = [
                    'id'=>$router['id'],
                    'title'=>$router['title'],
                    'path'=>$router['menu'] ? '#'.$router['menu'] : '',
                    'icon'=>'&'.$router['icon'],
                    'pid'=>$router['pid'],
                    'open'=>$router['open'] == 1 ? true : false,
                    'children'=>$children,
                ];
            }
        }

        return json($xml);

    }

    //路由配置项菜单
    public function opts()
    {
        $res = Router::where('status',1)->where('opts',1)->select();
        $xml = [];
        foreach($res as $router){
            /*if($router['pid'] == 0){
                $children = array();
                foreach ($res as $CRoute){
                    if($CRoute['pid'] == $router['id'] && $CRoute['main'] == 1){
                        $children[] = [
                            'id'=>$CRoute['id'],
                            'title'=>$CRoute['title'],
                            'path'=>'#'.$CRoute['menu'],
                            'icon'=>'&'.$CRoute['icon'],
                            'pid'=>$CRoute['pid'],
                            'open'=>false
                        ];
                    }
                }
                $xml[] = [
                    'id'=>$router['id'],
                    'title'=>$router['title'],
                    'path'=>$router['menu'] ? '#'.$router['menu'] : '',
                    'icon'=>'&'.$router['icon'],
                    'pid'=>$router['pid'],
                    'open'=>$router['open'] == 1 ? true : false,
                    'children'=>$children,
                ];
            }*/
            $xml[]= [
                'path'=>$router['menu'],
                'component'=>$router['router'],
                'name'=>$router['title']
            ];
        }


        return json($xml);
    }
}