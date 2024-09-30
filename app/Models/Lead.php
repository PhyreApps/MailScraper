<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'scraper_id',
        'scraped_from_url',
        'scraped_from_domain'
    ];

    public function scraper()
    {
        return $this->belongsTo(Scraper::class);
    }
}
