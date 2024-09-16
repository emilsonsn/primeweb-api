<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occurrence extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'occurrences';

    public $fillable = [
        'user_id',
        'date',
        'time',
        'status',
        'link',
        'observations',
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
}
