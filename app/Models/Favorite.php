<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\RecordsActivity;

class Favorite extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    public function favorited()
    {
        return $this->morphTo();
    }

    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }
}
