<?php

namespace Revisionable\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'name',
    ];
}
