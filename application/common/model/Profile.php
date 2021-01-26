<?php

namespace app\common\model;

use think\Model;

class Profile extends Model
{
    //主外键
    //定义管理员-档案关联关系
    public function profile()
    {
        return $this->hasOne('Profile', 'uid', 'id');
    }
//定义管理员-档案关联关系
    public function admin()
    {
        return $this->belongsTo('Admin', 'uid', 'id');
    }
}
