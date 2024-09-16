<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Segment extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'segments';

    public $fillable= [
        'user_id',
        'name',
        'status',
    ];

    public function user(){
        $this->belongsTo(User::class);
    }
}
