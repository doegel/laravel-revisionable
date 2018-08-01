<?php

namespace Revisionable\Traits;

/**
 * Trait HasRelationships
 * @package App\Packages\Revisionable
 */
trait HasRelationships
{
    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $localKey = $localKey ?: $this->getLocalKeyName();

        return parent::hasOne($related, $foreignKey, $localKey);
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $localKey = $localKey ?: $this->getLocalKeyName();

        return parent::morphOne($related, $name, $type, $id, $localKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $localKey = $localKey ?: $this->getLocalKeyName();

        return parent::hasMany($related, $foreignKey, $localKey);
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $localKey = $localKey ?: $this->getLocalKeyName();

        return parent::hasManyThrough($related, $through, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $localKey = $localKey ?: $this->getLocalKeyName();

        return parent::morphMany($instance, $name, $type, $id, $localKey);
    }

    /**
     * Returns the default local key for this model.
     * 
     * @return string
     */
    private function getLocalKeyName(): string
    {
        return $this->isRevisioned() 
            ? $this->getRevisionIdName() 
            : $this->getKeyName();
    }
}
