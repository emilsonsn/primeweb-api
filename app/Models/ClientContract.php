<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContract extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'number',
        'path',
        'date_hire',
        'number_words_contract',
        'service_type',
        'model',
        'observations',
        'client_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
