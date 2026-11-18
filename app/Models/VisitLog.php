<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'latitude_in'   => 'float',
        'longitude_in'  => 'float',
        'latitude_out'  => 'float',
        'longitude_out' => 'float',
        'time_in'       => 'datetime',
        'time_out'      => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
