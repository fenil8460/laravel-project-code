<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithUuid;

class SafeMemo extends Model
{
    use HasFactory,WithUuid;
    protected $table="safe_memo";

    protected $fillable = [
        'user_id',
        'reason',
        'safe_spam',
        'entry_by_id',
        'entry_by_nick_name',
        'followup',
        'approve_reason',
        'approve_for',
        'ip_address',
    ];

    protected $hidden = [
        'id'
     ];
}
