<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/11/13
 * Time: 15:29
 */

namespace app\admin\common;

use think\facade\Config;
use think\Db;

class User
{
    /**
     * 生成session的key
     * var string
     */
    private static $sessionKey;
    /**
     * 用户加密密钥
     * var string
     */
    private static $authKey;
    /**
     * 是否已初始化
     * var boolean
     */
    private static $handler = false;

    /**
     * 管理员用户表名
     * var string
     * */
    private static $table;

    /**
     * 管理员用户帐户字段名称,即$uuid
     * $name
     * */
    private static $name;

    /**
     * 初始化
     */
    public static function init(){
        self::$sessionKey = 'login_'.md5('user');
        self::$authKey = Config::get('crypt_key');
        self::$handler = true;  // 标记为初始化成功
        self::$table = Config::get('admin_table');
        self::$name = Config::get('admin_name');
    }

    /**
     * 内容信息定义
     * @param   string  $uid        用户ID
     * @param   string  $token      $user[id].$user[password]
     * @param   string  $uuid       用户account
     * */

    /**
     * 获取用户信息
     * @param  string  $uuid        uuid，为空，则获取当前登录用户的用户信息
     * @param  boolean $refresh     是否重新刷新，false为不刷新直接读取静态变量；true重新获取
     * return array|false
     */
    public static function user($uuid = '', $refresh = false){
        !self::$handler && self::init();
        // 设置静态变量
        static $users = array();
        // 如未设置uuid，则获取当前登录的用户uuid
        if (empty($uuid)) {
            if (!$uuid = self::logined_uuid()) return false;
        }
        // 读取静态变量（不刷新），如果存在，则直接返回
        if (!$refresh && array_key_exists($uuid, $users)) {
            return $users[$uuid];
        }
        // 获取用户信息
        $users[$uuid] = Db::table(self::$table)->where(self::$name,$uuid)->find();
        return $users[$uuid];
    }

    /**
     * 判断用户是否登录
     * return string|false 如登录，则返回uuid，否则false
     */

    public static function check(){
        !self::$handler && self::init(); // 初始化
        //获取并判断session存储的用户信息
        if (!$sArrs = session(self::$sessionKey)) {
            return false;
        }
        if (!array_key_exists('uid', $sArrs) || !array_key_exists('token', $sArrs)) {
            return false;
        }
        // 解密 并 获取uid
        $uid = decrypt($sArrs['uid'], self::$authKey);
        // 设置静态变量
        static $users = array();
        // 读取数据
        if (array_key_exists($uid, $users)) {
            $user = $users[$uid];
        } else {
            $user = $users[$uid] = Db::table(self::$table)->where(array('status' => 1, 'id' => $uid))->find();
        }
        if (!$user) {
            self::logout(); //登出
            return false;
        }
        // 校验token
        $tokenStr       = $user['id'].$user['password'];
        $verifyTokenStr = decrypt($sArrs['token'], self::$authKey); //待校验token
        if (!$verifyTokenStr || $verifyTokenStr != $tokenStr) {
            self::logout(); //登出
            return false;
        }
        return $user[self::$name];
    }

    /**
     * 用户登录
     * @param  string $uuid uuid
     * return boolean
     */

    public static function login($uuid){
        !self::$handler && self::init(); // 初始化
        if (empty($uuid)) return false;
        // 读取用户信息
        $user = self::user($uuid);
        if (!$user) return false;
        // 加密用户信息并生成session
        $tokenStr = $user['id'].$user['password'];
        $sArrs    = array(
            'uid'       => encrypt($user['id'], self::$authKey),
            'token'     => encrypt($tokenStr, self::$authKey),
            'timestamp' => time()
        );
        session(self::$sessionKey, $sArrs);
        return true;
    }

    /**
     * 登出
     */
    public static function logout(){
        !self::$handler && self::init(); // 初始化
        session(self::$sessionKey, null);
    }

    /**
     * 获取登录用户的uuid
     * return string      返回uuid
     */
    public static function logined_uuid(){
        static $uuid = null;
        if ($uuid == null) {
            $uuid = self::check();
        }
        return $uuid;
    }
}