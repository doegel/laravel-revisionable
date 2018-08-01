<?php

namespace Revisionable\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'name',
    ];
}
