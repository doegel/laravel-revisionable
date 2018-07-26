<?php

namespace Revisionable\Tests;

use Illuminate\Support\Facades\DB;
use Revisionable\Contracts\RevisionableContract;
use Illuminate\Foundation\Testing\WithFaker;
use Revisionable\Tests\Models\House;

class PerformanceTest extends TestCase
{
    use WithFaker;

    protected function tearDown()
    {
        // Since we can not use Refresh database trait for mongodb.
        House::truncate();

        parent::tearDown();
    }

    public function testInsertQueryMessurements()
    {
        DB::connection()->enableQueryLog();

        $this->prepareTestData();

        DB::connection()->disableQueryLog();

        $this->assertLessThan(10, count(DB::getQueryLog()));
    }

    public function testSelectQueryMessurements()
    {
        $this->prepareTestData();

        DB::connection()->enableQueryLog();

        House::all()->each(function (RevisionableContract $house) {
            // Query each stuff we would normaly use.
            $tmp = [
                'first' => $house->isFirstRevision(),
                'last' => $house->isLatestRevision(),
                'prev' => $house->previousRevision(),
                'next' => $house->nextRevision(),
            ];
        });

        DB::connection()->disableQueryLog();
        $this->assertLessThan(5, count(DB::getQueryLog()));
    }

    protected function prepareTestData()
    {
        $house = House::create(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
    }
}
