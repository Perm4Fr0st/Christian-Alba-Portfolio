<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    public const RESOURCE_KEY = 'skills';
    protected $table = self::RESOURCE_KEY;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'image'
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
