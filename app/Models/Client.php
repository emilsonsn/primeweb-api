<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'clients';


    protected $fillable = [
        'company',
        'domain',
        'cnpj',
        'client_responsable_name',
        'client_responsable_name_2',
        'cep',
        'street',
        'neighborhood',
        'city',
        'state',
        'monthly_fee',
        'payment_first_date',
        'duedate_day',
        'final_date',
        'observations',
        'segment_id',
        'consultant_id',
        'seller_id',
        'technical_id'
    ];

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function technical()
    {
        return $this->belongsTo(User::class, 'technical_id');
    }

    public function emails()
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function phones()
    {
        return $this->hasMany(ClientPhone::class);
    }

    public function contracts()
    {
        return $this->hasMany(ClientContract::class);
    }
}
