<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('user_id', auth()->user()->getAuthIdentifier());
            }
        });
    }

    public function scraper()
    {
        return $this->belongsTo(Scraper::class);
    }
}
