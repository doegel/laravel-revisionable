<?php

return [

    /*
     * When using the "RevisionableTrait" from this package, we need to know which
     * Eloquent models should be used to retrieve your permissions. Normally,
     * you would insert any model with the "RevisionableTrait" added here.
     */

    'models' => [
      //
    ],

    'column_names' => [

        /*
         * When using the "RevisionableTrait" from this package, we need to know the
         * name of the column for identifing cohesive models. We have chosen a basic
         * default value but you may easily change it to any name you like.
         */

        'revision_id' => 'revision_id',

        /*
         * When using the "RevisionableTrait" from this package, we need to know the
         * name of the column holding version information. We have chosen a basic
         * default value but you may easily change it to any name you like.
         */

        'revision_version' => 'revision_version',

        /*
         * When using the "RevisionableTrait" from this package, we need to know the
         * name of the column used to flag models as leading. We have chosen a basic
         * default value but you may easily change it to any name you like.
         */

        'revision_leading' => 'revision_leading',

    ],

];