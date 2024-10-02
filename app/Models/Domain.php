<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'scraper_id',
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
