<?php

namespace app\adminapi\controller;

use app\common\model\Admin;
use app\common\model\Role;
use think\Collection;
use think\Controller;
use think\Request;

class Auth extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = input();
        $where = [];
        if(!empty($params['keywords'])){
            $where['auth_name'] = ['like',"%{$params['keyowrd']}%"];
        }
        $list = \app\common\model\Auth::where($where)->select();
        $list = (new Collection($list))->toArray();
        if(!empty($params['type']) && $params['type'] == 'tree'){
            $list = get_tree_list($list);
        }else{
            $list = get_cate_list($list);
        }

        $this->ok($list);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
       $params = input();
       $validate = $this->validate($params,[
           'auth_name'=>'require',
           'pid'=>'require',
           'is_nav'=>'require',
       ]);
       if($validate !== true){
           $this->fail($validate,401);
       }
       if($params['pid'] == 0){
           $params['level'] = 0;
           $params['pid_path'] = 0;
           $params['auth_c'] = '';
           $params['auth_a'] = '';
       }else{
           $p_info = \app\common\model\Auth::field($params['pid']);
           if(empty($p_info)){
               $this->fail('数据异常');
           }
           $params['level'] = $p_info['level'] + 1;
           $params['pid_path'] = $p_info['pid_path']."_".$p_info['id'];
       }
       $auth = \app\common\model\Auth::create($params,true);
       $info = \app\common\model\Auth::find($auth['id']);
        $this->ok($info);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $auth = \app\common\model\Auth::get($id)->toArray();
        $this->ok($auth);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $params = input();
        if(empty($params['pid'])){
            $params['pid'] = 0;
        }
        if(empty($params['is_nav'])){
            $params['is_nav'] = $params['radio'];
        }
        $validate = $this->validate($params,[
            'auth_name'=>'require',
            'pid'=>'require',
            'is_nav'=>'require',
        ]);
        if($validate !== true){
            $this->fail($validate,401);
        }
        $auth = \app\common\model\Auth::find($id);
        if(empty($auth)){
            $this->fail('数据异常');
        }
        if($params['pid'] == 0){
            $params['level'] = 0;
            $params['pid_path'] = 0;
        }else{
            $p_auth = \app\common\model\Auth::field($params['pid']);
            if(empty($p_auth)){
                $this->fail('数据异常');
            }
            $params['level'] = $p_auth['level'] + 1;
            $params['pid_path'] = $p_auth['pid_path']."_".$p_auth['id'];
        }
        $auth = \app\common\model\Auth::create($params,true);
        $info = \app\common\model\Auth::find($auth['id']);
        $this->ok($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $total = \app\common\model\Auth::where('pid',$id)->count();
        if($total > 0){
            $this->fail('有子权限,无法删除');
        }
        \app\common\model\Auth::destroy($id);
        $this->ok();
    }

    public function nav(){
        $user_id = input('user_id');
        $info = Admin::find($user_id);
        $role_id = $info['role_id'];
        if($role_id == 1){
            $data = \app\common\model\Auth::where('is_nav',1)->select();
        }else{
            $role = Role::find($role_id);
            $role_auth_ids = $role['role_auth_ids'];
            $data = \app\common\model\Auth::where('is_nav',1)
                ->where('id','in',$role_auth_ids)
                ->select();
        }
        $data= (new Collection($data))->toArray();
        $data = get_tree_list($data);
        $this->ok($data);
    }
}
