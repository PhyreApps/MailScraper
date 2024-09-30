<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scraper extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'selector',
        'content',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function start()
    {

    }

    public function getStatusAttribute()
    {
        if (!empty($this->status)) {
            return $this->status;
        }
        return 'Waiting for start';
    }

}
