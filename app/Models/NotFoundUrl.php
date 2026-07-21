<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotFoundUrl extends Model
{
    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected $table = 'not_found_url';

    protected $fillable = [
        'path',
        'reportDate',
        'occurrences',
        'firstSeenAt',
        'lastSeenAt',
    ];

    protected function casts(): array
    {
        return [
            'reportDate' => 'date',
            'firstSeenAt' => 'datetime',
            'lastSeenAt' => 'datetime',
        ];
    }
}
