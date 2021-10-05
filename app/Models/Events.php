<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    public $table = 'events';
    public $fillable = ['event'];

    public function events_date ()
    {
        return $this->hasMany(EventsDate::class);
    }
}
