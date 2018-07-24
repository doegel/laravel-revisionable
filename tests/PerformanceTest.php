<?php

namespace Revisionable\Tests;

use App\Entities\House;
use Illuminate\Support\Debug\Dumper;
use Illuminate\Support\Facades\DB;
use Revisionable\Contracts\RevisionableContract;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PerformanceTest extends TestCase
{
    use WithFaker;
    
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
