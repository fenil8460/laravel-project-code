<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\WithUuid;
class Webhook extends Model
{
use SoftDeletes,WithUuid;

protected $dates = ['deleted_at'];
protected $table="webhooks";
protected $fillable = [
    'created_by_id',
    'company_id',
    'action',
    'url'
];
}
