<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "ext",
        "url",
        "type",
        "width",
        "height",
        "mime",
        "fileable_type",
        "fileable_id",
        "duration",
        "user_id",
        "custom_name",
        'notes'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }
}
