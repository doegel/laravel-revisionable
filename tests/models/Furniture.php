<?php

namespace Revisionable\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Furniture extends Model
{
    protected $fillable = [
        'name',
    ];
}
