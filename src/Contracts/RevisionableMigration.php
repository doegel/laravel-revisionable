<?php

namespace Revisionable\Contracts;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

abstract class RevisionableMigration extends Migration
{
    /**
     * Returns the table name we want to migrate onto.
     *
     * @return string
     */
    abstract public function getTableName() : string;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->unsignedInteger(config('revisionable.column_names.revision_id'))->nullable();
            $table->unsignedInteger(config('revisionable.column_names.revision_version'))->default(1);
            $table->boolean(config('revisionable.column_names.revision_leading'))->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->dropColumn([
                config('revisionable.column_names.revision_id'),
                config('revisionable.column_names.revision_version'),
                config('revisionable.column_names.revision_leading'),
            ]);
        });
    }
}
