<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientWordKey extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'word_key',
        'user_id',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}