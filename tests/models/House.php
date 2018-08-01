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

    /**
     * @return \Jenssegers\Mongodb\Relations\EmbedsMany
     */
    public function invoices()
    {
        return $this->embedsMany(Invoice::class);
    }

    /**
     * @return \Jenssegers\Mongodb\Relations\HasMany
     */
    public function furniture()
    {
        return $this->hasMany(Furniture::class);
    }

    /**
     * @return \Jenssegers\Mongodb\Relations\HasOne
     */
    public function owner()
    {
        return $this->hasOne(Owner::class);
    }
}
