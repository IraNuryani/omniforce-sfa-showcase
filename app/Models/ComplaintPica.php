<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintPica extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'complaint_picas';
    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
