<?php

namespace app\adminapi\controller;

use think\Controller;

class Login extends BaseApi
{
    /**
     * 获取验证码图片地址
     */
    public function captcha()
    {
        //验证码标识
        $uniqid = uniqid(mt_rand(100000, 999999));
        //返回数据 验证码图片路径、验证码标识
        $data = [
            'src' => captcha_src($uniqid),
            'uniqid' => $uniqid
        ];
       return json($data);
    }

    public function login()
    {
        //获取输入变量
        $param = input();
        $validate = $this->validate($param, [
            'username' => 'require',
            'password' => 'require',
            'code' => 'require',
            'uniqid' => 'require'
        ]);
        if($validate !== true){
            $this->error($validate);
        }
        //根据验证码标识，从缓存取出session_id 并重新设置session_id
        session_id(cache('session_id_'.$param['uniqid']));
        //进行验证码校验 使用手动验证方法
        if (captcha_check($param['code'], $param['uniqid'])) {
            //验证码错误
            return json(['code'=>200,'data'=>$param,'msg'=>'验证码失败']);
        }
//        根据用户名和密码（加密后的密码），查询管理员用户表
        $where = [
            'username' => $param['username'],
            'password' => encrypt_password($param['password'])
        ];
        $info = \app\common\model\Admin::where($where)->find();
        if(!$info){
            //用户名或者密码错误
            return json(['code'=>200,'data'=>'','msg'=>'用户名或者密码错误']);
        }
        $data['token'] = \tools\jwt\Token::getToken($info->id);
        $data['user_id'] = $info->id;
        $data['username'] = $info->username;
        $data['nickname'] = $info->nickname;
        $data['email'] = $info->email;
        session('token',$data['token']);
        //登录成功
        return json(['code'=>300,'data'=>$data,'msg'=>'success']);
    }

    public function logout()
    {
        //清空token  将需清空的token存入缓存，再次使用时，会读取缓存进行判断
        $token = \tools\jwt\Token::getRequestToken();
        $delete_token = cache('delete_token') ?: [];
        $delete_token[] = $token;
        cache('delete_token', $delete_token, 86400);
        return 1;
    }
}
