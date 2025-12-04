<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = ['recipient_id', 'name', 'description', 'price', 'url', 'purchased'];

    protected $casts = [
        'purchased' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
