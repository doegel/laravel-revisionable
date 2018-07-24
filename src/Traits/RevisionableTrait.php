<?php

namespace Revisionable\Traits;

use Revisionable\Contracts\RevisionableContract;
use Revisionable\LatestRevisionScope;

/**
 * Trait RevisionableTrait
 * @package App\Packages\Revisionable
 */
trait RevisionableTrait
{
    /**
     * Controls whenever a revision is saved.
     *
     * @var bool
     */
    public $revisioning = true;

    /**
     * Boot the trait by adding a observer to this model.
     */
    public static function bootRevisionableTrait()
    {
        // Install the observer, this drives the actual
        // implementation of this trait.
//        static::observe(RevisionObserver::class);

        // Make sure we always only query
        //  the latest revisions.
        static::addGlobalScope(new LatestRevisionScope);
    }

    /**
     * Gets the revision id column name.
     *
     * @return string
     */
    public function getRevisionIdName(): string
    {
        return config('revisionable.column_names.revision_id');
    }

    /**
     * Returns the revision id for the this revision.
     * This refers to the id of the
     * original model.
     *
     * @return int
     */
    public function getRevisionId(): ?int
    {
        return $this->{$this->getRevisionIdName()};
    }

    /**
     * Gets the revision version column name.
     *
     * @return string
     */
    public function getRevisionVersionName(): string
    {
        return config('revisionable.column_names.revision_version');
    }

    /**
     * Returns the version code for
     * the this revision.
     *
     * @return int
     */
    public function getRevisionVersion(): int
    {
        return intval($this->{$this->getRevisionVersionName()});
    }

    /**
     * Gets the revision leading column name.
     *
     * @return string
     */
    public function getRevisionLeadingName(): string
    {
        return config('revisionable.column_names.revision_leading');
    }

    /**
     * Rolls back to this revision. This will
     * delete all newer revisions. They
     * can not be restored later.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $this->{$this->getRevisionLeadingName()} = true;
        $this->withoutRevision(function ($model) {
            $model->save();
        });

        return static::revisionFromSameModel()
            ->where($this->getRevisionVersionName(), '>', $this->getRevisionVersion())
            ->delete();
    }

    /**
     * Whenever revisioning is enabled or not.
     *
     * @return bool
     */
    public function isRevisioningEnabled(): bool
    {
        return $this->revisioning;
    }

    /**
     * Enables the revisioning.
     *
     * @return void
     */
    public function enableRevisioning(): void
    {
        $this->revisioning = true;
    }

    /**
     * Disables the revisioning.
     *
     * @return void
     */
    public function disableRevisioning(): void
    {
        $this->revisioning = false;
    }

    /**
     * Execute a method of storage without revisioning.
     *
     * @param $func
     * @return mixed
     */
    public function withoutRevision(callable $func)
    {
        $this->disableRevisioning();

        // Passing this as argument for conveniance.
        $result = $func($this);

        $this->enableRevisioning();

        // Returning function result, also for conveniance.
        return $result;
    }

    /**
     * Checks whenever this is the latest
     * revision of a given model.
     *
     * @return bool
     */
    public function isLatestRevision(): bool
    {
        return boolval($this->{$this->getRevisionLeadingName()});
    }

    /**
     * Checks if this is the first -
     * or root - revision of a
     * given model.
     *
     * @return bool
     */
    public function isFirstRevision(): bool
    {
        return $this->getRevisionId() === null;
    }

    /**
     * Get the next newer revision of a
     * given model of this type.
     *
     * @return RevisionableContract
     */
    public function nextRevision(): ?RevisionableContract
    {
        return static::revisionFromSameModel()
            ->where($this->getRevisionVersionName(), $this->getRevisionVersion() + 1)
            ->first();
    }

    /**
     * Get the last older revision of a
     * given model of this type.
     *
     * @return RevisionableContract
     */
    public function previousRevision(): ?RevisionableContract
    {
        return static::revisionFromSameModel()
            ->where($this->getRevisionVersionName(), $this->getRevisionVersion() - 1)
            ->first();
    }

    /**
     * Get a specific revision of a given
     * model of this type.
     *
     * @param $query
     * @param int $version
     * @return mixed
     */
    public function scopeRevision($query, int $version)
    {
        return $query->revisionFromSameModel()
            ->where($this->getRevisionVersionName(), $version);
    }

    /**
     * Query the latest revision of this type.
     *
     * @param $query
     * @return mixed
     */
    public function scopeLatestRevision($query)
    {
        return $query->revisionFromSameModel()
            ->orderBy($this->getRevisionVersionName(), 'DESC')
            ->take(1);
    }

    /**
     * Query the first revision of this type.
     *
     * @param $query
     * @return mixed
     */
    public function scopeFirstRevision($query)
    {
        return $query->revisionFromSameModel()
            ->orderBy($this->getRevisionVersionName(), 'ASC')
            ->take(1);
    }

    /**
     * Locks the query to the set of revisions
     * of the current model.
     *
     * @param $query
     * @return mixed
     */
    public function scopeRevisionFromSameModel($query)
    {
        // For internal purposes, we must not use the global scope.
        $query->withoutGlobalScope(LatestRevisionScope::class);

        if ($this->isFirstRevision()) {
            // We query from the root or the first revision.
            // We search for every model that matches
            // the roots primary key.
            return $query->where($this->getRevisionIdName(), $this->getKey());
        } else {
            // We query from an arbitary descenedet in the revision chain.
            // We have to search for other models with the same
            // revision id and the root where the revision
            // id matches the primary key.
            return $query->where($this->getKeyName(), $this->getRevisionId())
                ->orWhere($this->getRevisionIdName(), $this->getRevisionId());
        }
    }
}
