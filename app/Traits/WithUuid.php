<?php

namespace App\Traits;
//use this where ever there is uu_id column are needed

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait WithUuid
{
    protected $isLockedUuid = true;

    /**
     * Add behavior to creating and saving Eloquent events.
     * @return void
     */
    public static function bootWithUuid()
    {
        // Create a UUID to the model if it does not have one
        static::creating(function (Model $model) {
            if (!$model->uu_id) {
                $model->uu_id = (string)Str::uuid();
            }
        });

        // Set original if someone try to change UUID on update/save existing model
        static::saving(function (Model $model) {
            $original_uu_id = $model->getOriginal('uu_id');
            if (!is_null($original_uu_id) && $model->isLockedUuid) {
                if ($original_uu_id !== $model->uu_id) {
                    $model->uu_id = $original_uu_id;
                }
            }
        });
    }
}


