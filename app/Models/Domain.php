<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'scraper_id',
    ];

    public function scraper()
    {
        return $this->belongsTo(Scraper::class);
    }
}
