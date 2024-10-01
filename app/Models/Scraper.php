<?php

namespace App\Models;

use App\Jobs\RunScraper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('user_id', auth()->user()->getAuthIdentifier());
            }
        });
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()->user()->getAuthIdentifier();
        });
    }

    public function start()
    {
        $this->status = 'QUEUED';
        $this->save();
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

}
