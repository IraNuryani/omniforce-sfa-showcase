<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function visitLogs()
    {
        return $this->hasMany(VisitLog::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
