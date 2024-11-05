<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordRecover extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'password_recovery';

    public $fillable= [
        'code',
        'is_active',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
