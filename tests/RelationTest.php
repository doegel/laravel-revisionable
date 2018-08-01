<?php

namespace Revisionable\Tests;

use Revisionable\Tests\Models\Furniture;
use Revisionable\Tests\Models\Invoice;
use Revisionable\Tests\Models\House;
use Revisionable\Tests\Models\Owner;
use Illuminate\Foundation\Testing\WithFaker;

class RelationTest extends TestCase
{
    use WithFaker;

    protected function tearDown()
    {
        // Since we can not use Refresh database trait for mongodb.
        House::truncate();
        Furniture::truncate();
        Owner::truncate();

        parent::tearDown();
    }

    public function testEmbeddedRelation()
    {
        $house = $this->prepareTestData(function ($house) 
        {
          $invoice = new Invoice(['name' => $this->faker->domainWord]);
          $house->invoices()->save($invoice);
        });

        $this->assertNotNull($house->invoices);
        $this->assertEquals(1, $house->invoices->count());
    }

    public function testHasManyRelation()
    {
        $house = $this->prepareTestData(function ($house) 
        {
          $furniture = new Furniture(['name' => $this->faker->domainWord]);
          $house->furniture()->save($furniture);
        });

        $this->assertNotNull($house->furniture);
        $this->assertEquals(1, $house->furniture->count());
    }

    public function testHasOneRelation()
    {
        $house = $this->prepareTestData(function ($house) 
        {
          $owner = new Owner(['name' => $this->faker->name]);
          $house->owner()->save($owner);
        });

        $this->assertNotNull($house->owner);
        $this->assertInstanceOf(Owner::class, $house->owner);
    }

    protected function prepareTestData($relation)
    {
        // Make sure we got some revision to check if data 
        // flows correctly along revisions.
        $revs = 3;

        $house = House::create(['name' => $this->faker->domainWord]);

        // Make sure we call our relation callback on the original model.
        $relation($house);

        for ($i = 1; $i < $revs; $i++) {
            $house->update(['name' => $this->faker->domainWord]);
        }

        return $house;
    }
}
