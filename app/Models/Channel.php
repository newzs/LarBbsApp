<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded=[];
    protected $with = ['creator'];

    public function creator()
    {
        return $this->belongsTo(User::class,'user_id'); // 使用 user_id 字段进行模型关联
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
