<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10
 * Time: 22:16
 */

namespace app\admin\common;

use app\admin\model\Admin;
class Auth
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
     * 初始化
     */
    public static function init(){
        self::$sessionKey = 'login_'.md5('user');
        self::$authKey    = config('AUTH_USER_KEY');
        self::$handler = true;  // 标记为初始化成功
    }

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
        // 如为设置uuid，则获取当前登录的用户uuid
        if (empty($uuid)) {
            if (!$uuid = self::logined_uuid()) return false;
        }
        // 读取静态变量（不刷新），如果存在，则直接返回
        if (!$refresh && array_key_exists($uuid, $users)) {
            return $users[$uuid];
        }
        // 获取用户信息
        $users[$uuid] = Admin::where(array('ID' => $uuid))->find();
        return $users[$uuid];
    }

    /**
     * 判断用户是否登录
     * return string|false 如登录，则返回uuid，否则false
     */
    public static function check(){
        !self::$handler && self::init(); // 初始化
        if (!$sArrs = session(self::$sessionKey)) {
            return false;
        }
        if (!array_key_exists('uid', $sArrs) ||
            !array_key_exists('token', $sArrs)) {
            return false;
        }
        // 获取uid
        Crypt::init();
        $uid = Crypt::decrypt($sArrs['uid'], self::$authKey);
        // 设置静态变量
        static $users = array();
        // 读取数据
        if (array_key_exists($uid, $users)) {
            $user = $users[$uid];
        } else {
            $user = $users[$uid] = Admin::where(array('status' => 1, 'id' => $uid))->find();
        }
        if (!$user) {
            self::logout(); //登出
            return false;
        }
        // 校验token
        $tokenStr       = $user['id'].$user['password'];
        $verifyTokenStr = Crypt::decrypt($sArrs['token'], self::$authKey); //待校验token
        if (!$verifyTokenStr || $verifyTokenStr != $tokenStr) {
            self::logout(); //登出
            return false;
        }
        return $user['uuid'];
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
        // 生成session
        Crypt::init();
        $tokenStr = $user['id'].$user['password'];
        $sArrs    = array(
            'uid'       => Crypt::encrypt($user['id'], self::$authKey),
            'token'     => Crypt::encrypt($tokenStr, self::$authKey),
            'timestamp' => time()
        );
        session(self::$sessionKey, $sArrs);
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
     */
    public static function logined_uuid(){
        static $uuid = null;
        if ($uuid == null) {
            $uuid = self::check();
        }
        return $uuid;
    }

    /**
     * 获取用户所属管理组（后台专用）
     * @param  string  $uuid    uuid（若为空，则获取当前登录用户的管理组）
     * @param  boolean $refresh 是否刷新
     * return array|false
     */
    public static function roles($uuid = '', $refresh = false){
        // 设置静态变量
        static $rolesS = array();
        // 如为设置uuid，则获取当前登录的用户uuid
        if (empty($uuid)) {
            if (!$uuid = self::logined_uuid()) return false;
        }
        // 读取静态变量（不刷新），如果存在，则直接返回
        if (!$refresh && array_key_exists($uuid, $rolesS)) {
            return $rolesS[$uuid];
        }
        $pre = C('DB_PREFIX'); // 表前缀
        $rolesS[$uuid] =  M('mall_roles r')
            ->field('r.*')
            ->cache(true, 60)
            ->join($pre . 'mall_users_roles ur on ur.role_id = r.id')
            ->join($pre . 'users u on u.id = ur.user_id')
            ->where(array('u.uuid' => $uuid))
            ->select();
        return $rolesS[$uuid];
    }
    /**
     * 获取用户可用管理权限（后台专用）
     * @param  string  $uuid    uuid（若为空，则获取当前登录用户的用户权限）
     * @param  boolean $refresh 是否刷新
     * return array|false      已经处理重复清除
     */
    public static function rules($uuid, $refresh = false){
        // 设置静态变量
        static $rulesS = array();
        // 如为设置uuid，则获取当前登录的用户uuid
        if (empty($uuid)) {
            if (!$uuid = self::logined_uuid()) return false;
        }
        // 读取静态变量（不刷新），如果存在，则直接返回
        if (!$refresh && array_key_exists($uuid, $rulesS)) {
            return $rulesS[$uuid];
        }
        $pre = C('DB_PREFIX'); // 表前缀
        $rulesS[$uuid] =  M('mall_rules ru')
            ->field('ru.*')
            ->cache(true, 60)
            ->distinct(true)
            ->join($pre . 'mall_roles_rules rr on rr.rule_id = ru.id')
            ->join($pre . 'mall_users_roles ur on ur.role_id = rr.role_id')
            ->join($pre . 'users us on us.id = ur.user_id')
            ->where(array('us.uuid' => $uuid, 'ru.status' => 1))
            ->select();
        return $rulesS[$uuid];
    }
    /**
     * 获取所有可用的权限规则
     * @param  boolean $refresh 是否刷新
     * return array|false
     */
    public static function allRules($refresh = false){
        // 设置静态变量
        static $allRules = null;
        // 读取静态变量（不刷新），如果存在，则直接返回
        if (!$refresh && $allRules != null) {
            return $allRules;
        }
        $allRules = M('mall_rules')->where(array('status' => 1))->select();
        return $allRules;
    }
    /**
     * 权限校验
     * @param  string $rule 权限规则(如果不存在预置的权限规则，则直接通过，可用)
     * @param  string $uuid uuid（若为空，则获取当前登录用户的用户权限）
     * return boolean
     */
    public static function verifyRule($rule, $uuid = ''){
        !self::$handler && self::init();
        // A. 获取所有权限
        $allRules = self::allRules();
        // A.1 转换为小写
        $allRulesTmp = array();
        foreach($allRules as $v){
            $allRulesTmp[] = strtolower($v['value']);
        }
        $allRules = $allRulesTmp;
        // B. 待校验的规则
        if (!is_string($rule)) {
            return false;
        }
        $rule = strtolower($rule);  //转换为小写
        // C. 判断当前校验的权限是否需要权限(如果不存在预置的权限规则，则直接通过，可用)
        if (!in_array($rule, $allRules)) {
            return true;
        }
        // D. 获取该用户所有权限
        // D.1 获取该用户所有权限规则
        $userRules = self::rules($uuid); // 如果uuid为空，则获取当前登录用户
        // D.2 转换为小写, 并获取权限规则
        $userRulesTmp = array();
        foreach($userRules as $v){
            $userRulesTmp[] = strtolower($v['value']);
        }
        $userRules = $userRulesTmp;
        // E. 如果在预置的权限规则里。则校验当前用户是否含有改权限
        if (in_array($rule, $userRules)) {
            return true;
        }
        return false;
    }

}