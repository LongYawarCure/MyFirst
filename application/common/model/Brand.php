<?php

namespace app\common\model;

use think\Model;

class Brand extends Model
{
    public function brands()
    {
        return $this->hasMany('Brand', 'cate_id', 'id');
    }

    public function category()
    {
        return $this->BelongsTo('Category', 'cate_id', 'id');
    }
}
