<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Admin extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收参数  keyword  page
        $params = input();
        $where = [];
        //搜索条件
        if(!empty($params['keyword'])){
            $keyword = $params['keyword'];
            $where['t1.username'] = ['like', "%$keyword%"];
            $list = \app\common\model\Admin::alias('t1')
                ->join('pyg_role t2', 't2.id=t1.id', 'left')
                ->field('t1.*, t2.role_name')
                ->where($where)
                ->select();
            return  json(['data'=>$list]);
        }

        //分页查询（包含搜索）
        $page = empty($params['page'])?1:$params['page'];
        //$list = \app\common\model\Admin::where($where)->paginate(10);
        //SELECT t1.*, t2.role_name FROM pyg_admin t1 left join pyg_role t2 on t1.role_id = t2.id where username like '%a%' limit 0, 2;
        $list = \app\common\model\Admin::alias('t1')
            ->join('pyg_role t2', 't2.id=t1.id', 'left')
            ->field('t1.*, t2.role_name')
            ->where($where)
            ->paginate(2,false,['page'=>$page]);
        //返回数据
       return  json($list);

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
             
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //查询数据
        $info = \app\common\model\Admin::find($id);
        //返回数据
        $this->ok($info);
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
        if($id == 1){
            $this->fail('超级管理员，不能修改');
        }
        //接收数据
        $params = input();
        if(!empty($params['type']) && $params['type'] == 'reset_pwd'){
            $password = encrypt_password('123456');
            \app\common\model\Admin::update(['password' => $password], ['id' => $id], true);
        }else{
            //参数检测
            $validate = $this->validate($params, [
                'email|邮箱' => 'email',
                'role_id|所属角色' => 'integer|gt:0',
                'nickname|昵称' => 'max:50',
            ]);
            if($validate !== true){
                $this->fail($validate);
            }
            //修改数据（用户名不让改）
            unset($params['username']);
            unset($params['password']);
            \app\common\model\Admin::update($params, ['id' => $id], true);
        }
        $info = \app\common\model\Admin::find($id);
        //返回数据
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
        //删除数据（不能删除超级管理员admin、不能删除自己）
        if($id == 1){
            $this->fail('不能删除超级管理员');
        }
        if($id == input('user_id')){
            $this->fail('删除自己? 你在开玩笑嘛');
        }
        \app\common\model\Admin::destroy($id);
        //返回数据
        $this->ok();
    }
}
