<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class MessageTemplate extends Model
{
    use HasFactory,WithUuid;

    protected $table = "message_templates";

    protected $fillable = [
        'nick_name',
        'template_text',
        'company_id',
        'user_id',
    ];

    protected $hidden = [
        'id'
     ];

    
}
