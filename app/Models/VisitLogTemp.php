<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLogTemp extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'visit_logs_temp';
    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'latitude_in'   => 'float',
        'longitude_in'  => 'float',
        'latitude_out'  => 'float',
        'longitude_out' => 'float',
        'sync_attempts' => 'integer',
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
