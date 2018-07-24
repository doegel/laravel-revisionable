<?php

namespace Revisionable\Contracts;

interface RevisionableContract
{
    /**
     * Gets the primary key column name.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Gets the primary key value.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Gets the revision id column name.
     *
     * @return string
     */
    public function getRevisionIdName() : string;

    /**
     * Returns the revision id for the this revision.
     * This refers to the id of the
     * original model.
     *
     * @return int
     */
    public function getRevisionId() : ?int;

    /**
     * Gets the revision version column name.
     *
     * @return string
     */
    public function getRevisionVersionName() : string;

    /**
     * Returns the version code for
     * the this revision.
     *
     * @return int
     */
    public function getRevisionVersion() : int;

    /**
     * Gets the revision leading column name.
     *
     * @return string
     */
    public function getRevisionLeadingName() : string;

    /**
     * Rolls back to this revision. This will
     * delete all newer revisions. They
     * can not be restored later.
     *
     * @return bool
     */
    public function rollback() : bool;

    /**
     * Whenever revisioning is enabled or not.
     *
     * @return bool
     */
    public function isRevisioningEnabled() : bool;

    /**
     * Enables the revisioning.
     *
     * @return void
     */
    public function enableRevisioning() : void;

    /**
     * Disables the revisioning.
     *
     * @return void
     */
    public function disableRevisioning() : void;

    /**
     * Execute a method of storage without revisioning.
     *
     * @param $func
     * @return mixed
     */
    public function withoutRevision(callable $func);

    /**
     * Checks whenever this is the latest
     * revision of a given model.
     *
     * @return bool
     */
    public function isLatestRevision() : bool;

    /**
     * Checks if this is the first -
     * or root - revision of a
     * given model.
     *
     * @return bool
     */
    public function isFirstRevision() : bool;

    /**
     * Get the next newer revision of a
     * given model of this type.
     *
     * @return RevisionableContract
     */
    public function nextRevision() : ?RevisionableContract;

    /**
     * Get the last older revision of a
     * given model of this type.
     *
     * @return RevisionableContract
     */
    public function previousRevision() : ?RevisionableContract;

    /**
     * Query a specific revision of a given
     * model of this type.
     *
     * @param $query
     * @param int $version
     * @return mixed
     */
    public function scopeRevision($query, int $version);

    /**
     * Query the latest revision of this type.
     *
     * @param $query
     * @return mixed
     */
    public function scopeLatestRevision($query);

    /**
     * Query the first revision of this type.
     *
     * @param $query
     * @return mixed
     */
    public function scopeFirstRevision($query);

    /**
     * Locks the query to the set of revisions
     * of the current model.
     *
     * @param $query
     * @return mixed
     */
    public function scopeRevisionFromSameModel($query);
}
