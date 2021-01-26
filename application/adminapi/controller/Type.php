<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Type extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询数据
        $list = \app\common\model\Type::select();
        //返回数据
        $this->ok($list);
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询一条数据（包含规格信息、规格值、属性信息）  注意：with方法多个关联，逗号后不要加空格
        $info = \app\common\model\Type::with('specs,specs.spec_values,attrs')->find($id);
        //返回数据
        $this->ok($info);
        //相当于以下代码--不使用关联模型
        /*$info = \app\common\model\Type::find($id);
        $specs = \app\common\model\Spec::where('type_id', $id)->select();
        foreach($specs as &$spec){
            // $spec['id']   对应规格值表的spec_id
            $spec['spec_values'] = \app\common\model\SpecValue::where('spec_id', $spec['id'])->select();
        }
        unset($spec);
        $attrs = \app\common\model\Attribute::where('type_id', $id)->select();
        $info['specs'] = $specs;
        $info['attrs'] = $attrs;
        $this->ok($info);*/
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断是否有商品在使用该商品类型
        $goods = \app\common\model\Goods::where('type_id', $id)->find();
        if($goods){
            $this->fail('正在使用中，不能删除');
        }
        //开启事务
        \think\Db::startTrans();
        try{
            //删除数据 （商品类型、类型下的规格名、类型下的规格值、类型下的属性）
            \app\common\model\Type::destroy($id);
            \app\common\model\Spec::destroy(['type_id', $id]);
            //\app\common\model\Spec::where('type_id', $id)->delete();
            \app\common\model\SpecValue::destroy(['type_id', $id]);
            \app\common\model\Attribute::destroy(['type_id', $id]);
            //提交事务
            \think\Db::commit();
            //返回数据
            $this->ok();
        }catch(\Exception $e){
            //回滚事务
            \think\Db::rollback();
            //获取错误信息
            //$msg = $e->getMessage();
            //$this->fail($msg);
            $this->fail('删除失败');
        }
    }
}
