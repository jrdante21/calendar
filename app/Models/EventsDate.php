<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsDate extends Model
{
    use HasFactory;
    public $table = 'events_date';
    protected $guarded = [];
    protected $hidden = ['events_id'];
    
    public function events ()
    {
        return $this->belongsTo(Events::class);
    }
}
