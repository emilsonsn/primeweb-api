<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'logs';

    public $fillable = [
        'user_id',
        'ip',
        'action',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
