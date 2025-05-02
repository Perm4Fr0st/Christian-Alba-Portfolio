<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    public const RESOURCE_KEY = 'projects';
    protected $table = self::RESOURCE_KEY;

    protected $keyType = 'string';

    protected $fillable = [
        'skill_id',
        'name',
        'image',
        'link'
    ];

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }
}
