<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneCall extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'phone_calls';

    public $fillable = [
        'user_id',
        'company',
        'phone',
        'domain',
        'email',
        'return_date',
        'return_time',
        'observations',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function occurrences(){
        return $this->hasMany(Occurrence::class);
    }
}