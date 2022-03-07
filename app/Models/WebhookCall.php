<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;


class WebhookCall extends Model
{
    use HasFactory,WithUuid;
    protected $table="webhook_calls";

    protected $fillable = [
        'name',
        'url',
        'headers',
        'payload',
        'exception',
    ];
    
    protected $hidden = [
        'id'
     ];
}
