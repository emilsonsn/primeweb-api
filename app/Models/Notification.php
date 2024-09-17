<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'notifications';

    public $fillable = [
        'user_id',
        'message',
        'is_seen',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
  
}
