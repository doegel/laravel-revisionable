<?php

namespace Revisionable\Tests;

use App\Entities\House;
use Revisionable\LatestRevisionScope;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ModelTest extends TestCase
{
    use WithFaker;

    public function setUp()
    {
        $this->markTestSkipped(
            'Does not support mongo db'
        );
    }

    public function testInsertFirstRevision()
    {
        $house = $this->prepareTestData(1);

        $this->assertNull($house->revision_id);
        $this->assertEquals($house->revision_version, 1);

        $this->assertNull($house->previousRevision());
        $this->assertNull($house->nextRevision());

        $this->assertTrue($house->isFirstRevision());
        $this->assertTrue($house->isLatestRevision());
    }

    public function testUpdateCreatesRevision()
    {
        $house = $this->prepareTestData(1);
        $firstHouse = clone $house;

        $house->update(['name' => $this->faker->domainWord]);

        $this->assertEquals($house->revision_id, $firstHouse->id);
        $this->assertEquals($house->revision_version, 2);

        $this->assertNotNull($house->previousRevision());
        $this->assertNull($house->nextRevision());

        $this->assertFalse($house->isFirstRevision());
        $this->assertTrue($house->isLatestRevision());

        $houses = House::withoutGlobalScope(LatestRevisionScope::class)->get();
        $this->assertEquals(2, count($houses));
    }

    public function testRevisionRollback()
    {
        $house = $this->prepareTestData(5);

        $revisioned = $house->revision(3)->first();
        $success = $revisioned->rollback();

        $revisioned->fresh();

        $this->assertTrue($success);
        $this->assertEquals($revisioned->revision_version, 3);

        $this->assertNotNull($revisioned->previousRevision());
        $this->assertNull($revisioned->nextRevision());

        $this->assertFalse($revisioned->isFirstRevision());
        $this->assertTrue($revisioned->isLatestRevision());

        $houses = House::withoutGlobalScope(LatestRevisionScope::class)->get();
        $this->assertEquals(3, count($houses));
    }

    public function testDisableRevisionMethods()
    {
        $house = $this->prepareTestData(1);

        $house->disableRevisioning();

        // Test with updates.
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);
        $house->update(['name' => $this->faker->domainWord]);

        // Test with a plain save.
        $house->save();

        $house->enableRevisioning();

        $houses = House::withoutGlobalScope(LatestRevisionScope::class)->get();
        $this->assertEquals(1, count($houses));
    }

    public function testDisableRevisionClosure()
    {
        $house = $this->prepareTestData(1);

        $result = $house->withoutRevision(function ($model) {
            // Test with updates.
            $model->update(['name' => $this->faker->domainWord]);
            $model->update(['name' => $this->faker->domainWord]);
            $model->update(['name' => $this->faker->domainWord]);
            $model->update(['name' => $this->faker->domainWord]);

            // Test with a plain save.
            $model->save();
        });

        $houses = House::withoutGlobalScope(LatestRevisionScope::class)->get();
        $this->assertEquals(1, count($houses));
    }

    public function testDisableRevisionClosureReturnValue()
    {
        $house = $this->prepareTestData(1);

        $result = $house->withoutRevision(function () {
            // Set a return val to test later.
            return 'billow';
        });

        // Test return value.
        $this->assertEquals('billow', $result);
    }

    public function testScopeSpecificRevision()
    {
        $house = $this->prepareTestData(5);

        $revisioned = $house->revision(3)->first();

        $this->assertEquals($revisioned->revision_version, 3);

        $this->assertNotNull($revisioned->previousRevision());
        $this->assertNotNull($revisioned->nextRevision());

        $this->assertFalse($revisioned->isFirstRevision());
        $this->assertFalse($revisioned->isLatestRevision());
    }

    public function testScopeLatestRevision()
    {
        $house = $this->prepareTestData(5);

        $revisioned = $house->latestRevision()->first();

        $this->assertEquals($revisioned->revision_version, 5);

        $this->assertNotNull($revisioned->previousRevision());
        $this->assertNull($revisioned->nextRevision());

        $this->assertFalse($revisioned->isFirstRevision());
        $this->assertTrue($revisioned->isLatestRevision());
    }

    public function testScopeFirstRevision()
    {
        $house = $this->prepareTestData(5);

        $revisioned = $house->firstRevision()->first();

        $this->assertEquals($revisioned->revision_version, 1);

        $this->assertNull($revisioned->previousRevision());
        $this->assertNotNull($revisioned->nextRevision());

        $this->assertTrue($revisioned->isFirstRevision());
        $this->assertFalse($revisioned->isLatestRevision());
    }

    public function testSelectsLatestRevision()
    {
        $this->prepareTestData(2);

        $houses = House::all();

        $this->assertEquals(1, count($houses));
    }

    protected function prepareTestData($revs)
    {
        $house = factory(House::class)->make();
        
        for ($i = 1; $i < $revs; $i++) {
            $house->update(['name' => $this->faker->domainWord]);
        }

        return $house;
    }
}
