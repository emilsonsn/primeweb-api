<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'contacts';

    public $fillable = [
        'user_id',
        'company',
        'domain',
        'responsible',
        'origin',
        'return_date',
        'return_time',
        'cnpj',
        'cep',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'observations',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function phones(){
        return $this->hasMany(ContactPhone::class);
    }

    public function emails(){
        return $this->hasMany(ContactEmail::class);
    }

    public function segments(){
        return $this->hasMany(ContactSegment::class);
    }
}