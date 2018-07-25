<?php

namespace Revisionable;

use Revisionable\Contracts\RevisionableContract;
use MongoDB\BSON\ObjectId;

/**
 * Class RevisionObserver
 * @package App\Packages\Revisionable
 *
 * TODO: deleting, restoring
 */
class RevisionObserver
{
    /**
     * Listen to the model creating event.
     *
     * @param RevisionableContract $model
     * @return void
     */
    public function creating(RevisionableContract $model)
    {
        if ($model->getRevisionVersion() == null) {
            // Model is newly created. So we set
            // information for the first revison.
            $model->{$model->getRevisionIdName()} = null;
            $model->{$model->getRevisionLeadingName()} = true;
            $model->{$model->getRevisionVersionName()} = 1;
        }

        // Else; could be the case of saving a new revision
        // as the model to be saved already
        // has a revision version.
    }

    /**
     * Listen to the model updating event.
     *
     * @param RevisionableContract $model
     * @return bool
     */
    public function updating(RevisionableContract $model)
    {
        if ($model->isRevisioningEnabled()) {
            // Make sure the chain's integrity is alright.
            $this->regenerateRevisionChain($model);

            // Manage the revision attributes.
            $this->hoistRevision($model);

            // Bail out of updating the current model.
            return false;
        }
    }

    /**
     * This method takes care of the leading
     * revision flag for the revision chain.
     *
     * @param RevisionableContract $model
     */
    protected function regenerateRevisionChain(RevisionableContract $model)
    {
        /** @var RevisionableContract $copy */

        // Making a new instance with the exact same data.
        $copy = $model->newInstance([], true);
        $copy->forceFill($model->getOriginal());

        // Saving the model as an update, as we keept the
        // primary key and set "exists" to true.
        // The only thing we want to change
        // is the leading flag.
        $copy->{$model->getRevisionLeadingName()} = false;
        $copy->withoutRevision(function ($model) {
            $model->save();
        });
    }

    /**
     * Configures the model to save the new revision
     * as a new entry in the database.
     *
     * @param RevisionableContract $model
     */
    protected function hoistRevision(RevisionableContract $model)
    {
        // We create a copy of the model update
        // the revision information and then
        // store this as a new entry.
        $model->{$model->getRevisionIdName()} = $model->getRevisionId() ?? $model->getKey();
        $model->{$model->getRevisionLeadingName()} = true;
        $model->{$model->getRevisionVersionName()}++;

        // Reset intrinsic model data. So we are
        // able to save this model as a new
        // dataset in the table.
        $model->{$model->getKeyName()} = new ObjectId();
        $model->exists = false;

        // Store the model.
        $model->save();
    }
}
