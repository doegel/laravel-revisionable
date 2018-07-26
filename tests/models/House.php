<?php

namespace Revisionable\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Revisionable\Contracts\RevisionableContract;
use Revisionable\Traits\RevisionableTrait;

class House extends Model implements RevisionableContract
{
    use RevisionableTrait;

    protected $fillable = [
        'name',
    ];
}
