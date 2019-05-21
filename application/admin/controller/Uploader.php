<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/12/6
 * Time: 17:24
 */

namespace app\admin\controller;


use think\facade\App;
use think\facade\Config;

use think\facade\Env;



class Uploader
{


    /*
     * uploader             图像上传
     * param $genre  int     图片上传类型
     *      1   =》         layui.edit 上传使用返回
     *      0   =》         正常使用返回（默认）
     * return               json
     * */
    public function uploader($genre=0){

        header('content-type:text/plain;charset=utf-8');
        //定义站点根目录
        define('ROOT_PATH', str_replace("\\", '/',Env::get('root_path')));
        //定义附件上传路径
        define('ATTACHMENTS_PATH', ROOT_PATH . "public/uploads/");
        //定义日期目录
        define('DATE_PATH',date('Y').date('m').date('d').'/');
        //完整上传路径
        define('UPLOAD_PATH',ATTACHMENTS_PATH.DATE_PATH);

        //判断附件文件夹是否存在，不存在则创建
        if (!is_dir(ATTACHMENTS_PATH)) {
            mkdir(ATTACHMENTS_PATH);
        }
        //判断当天附件文件夹是否存在，不存在则创建
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH);
        }

        if ($_FILES['file']['error'] > 0) {
            $return = array();
            if($genre){
                $return['code'] = 1;
                $return['msg'] = '上传失败!';
                $return['data']['src'] = '';
            }else{
                $return['error'] = 1;
                $return['message'] = '上传失败!';
            }

            exit(json_encode($return));
        }

        //判断被上传的图片大小是否合法
        /**
         * getimagesize()根据图片的绝对路径（必须是绝对路径），获取这个图片的信息。返回的是一个数组：
         * 0=>图片的宽
         * 1=>图片的高
         * 2=>返回的是数字，其中1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
         * 3=>height="yyy" width="xxx"
         * 'bits'=>给出的是图像的每种颜色的位数，二进制格式
         * 'channels'=>给出的是图像的通道值，RGB 图像默认是 3
         * 'mime'=>类似于'image/jpeg'的MIME信息
         */
        $source_info = getimagesize($_FILES['file']['tmp_name']);
        if (isset($_REQUEST['minWidth'])) {
            if ($source_info[0] < $_REQUEST['minWidth']) {
                $return = array();
                if($genre){
                    $return['code'] = 1;
                    $return['msg'] = '图片尺寸太小!';
                    $return['data']['src'] = '';
                }else{
                    $return['error'] = 1;
                    $return['message'] = '图片尺寸太小';
                }
                exit(json_encode($return));
            }
        }
        if (isset($_REQUEST['minHeight'])) {
            if ($source_info[1] < $_REQUEST['minHeight']) {
                $return = array();
                if($genre){
                    $return['code'] = 1;
                    $return['msg'] = '图片尺寸太小!';
                    $return['data']['src'] = '';
                }else{
                    $return['error'] = 1;
                    $return['message'] = '图片尺寸太小';
                }
                exit(json_encode($return));
            }
        }

        //获取后缀名(这里不用$_FILES["file"]["type"]去获取文件的MIME类型来判断文件格式，因为flash上传文件的MIME类型统一都是"application/octet-stream")
        //$suffix = '.' . pathinfo($_FILES['file']['name'])['extension']; //$_FILES['file']['name']是上传文件的原始文件名称
        $pathinfo = pathinfo($_FILES['file']['name']);
        $suffix = '.' . $pathinfo['extension']; //$_FILES['file']['name']是上传文件的原始文件名称

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $src = uniqid() . $suffix;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], UPLOAD_PATH . $src)) {
                $return = array();
                if($genre){
                    $return['code'] = 1;
                    $return['msg'] = '上传失败!';
                    $return['data']['src'] = '';
                }else{
                    $return['error'] = 1;
                    $return['message'] = '上传失败!';
                }
                exit(json_encode($return));
            } else {
                $return = array();
                if(Config::get('website_url') == 'localhost' || Config::get('website_url') == '127.0.0.1'){
                    $src_url="/uploads/".DATE_PATH. $src;
                }else{
                    $src_url=Config::get('website_url')."/uploads/".DATE_PATH. $src;
                }
                if($genre){
                    $return['code'] = 0;
                    $return['msg'] = '上传成功!';
                    $return['data']['src'] = $src_url;
                }else{
                    $return['error'] = 0;
                    $return['message'] = '上传成功';
                    $return['url'] = $src_url;
                    $return['source_path'] = UPLOAD_PATH . $src;
                    if (isset($_REQUEST['uid'])) {
                        $return['uid'] = $_REQUEST['uid'];
                    }
                }
                exit(json_encode($return));
            }
        }


    }
}