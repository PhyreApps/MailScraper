<?php

namespace App\Models;

use App\Jobs\RunScraper;
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
