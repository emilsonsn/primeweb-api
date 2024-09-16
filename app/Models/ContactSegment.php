<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactSegment extends Model
{
    use HasFactory;



    public $table = 'contact_segments';
    public $timestamps= false;
    
    public $fillable = [
        'contact_id',
        'segment_id',
    ];

    public function contact(){
        return $this->belongsTo(Contact::class);
    }

    public function segment(){
        return $this->belongsTo(Segment::class);
    }
}
