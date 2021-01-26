<?php

namespace app\adminapi\controller;

use app\common\model\Admin;
use think\Collection;
use think\Controller;
use think\Request;

class Role extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $list = \app\common\model\Role::where('id','>',1)->select();
        foreach ($list as $k=>$v){
            $auths = \app\common\model\Auth::where('id','in',$v['role_auth_ids'])->select();
            $auths = (new Collection($auths))->toArray();
            $auths = get_tree_list($auths);
            $list[$k]['role_auths'] = $auths;
        }
        unset($v);
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
           'role_name' =>'require',
           'auth_ids'=>'require'
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        $params['role_auth_ids'] = $params['auth_ids'];
        $role = \app\common\model\Role::create($params,true);
        $info = \app\common\model\Role::find($role['id']);
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
        $info = \app\common\model\Role::find($id);
        $this->ok($info);
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
        $validate = $this->validate($params,[
            'role_name' =>'require',
            'auth_ids'=>'require'
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        $params['role_auth_ids'] = $params['auth_ids'];
        \app\common\model\Role::update($params,['id'=>$id],true);
        $info = \app\common\model\Role::find($id);
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
        if($id == 1){
            $this->fail('该角色无法删除');
        }
        $total = Admin::where('role_id',$id)->count();
        if($total > 0 ){
            $this->fail('角色正在使用中无法删除');
        }
        \app\common\model\Role::destroy($id);
        $this->ok();
    }
}
