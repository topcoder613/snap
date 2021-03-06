<?php

namespace Tests\Feature;

use App\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Observation;
use App\User;
use DB;
use Tests\TestCase;

class CollectionsTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * Test adding observations to a collection
     *
     * @group collection
     */

    public function testGettingCollectionIndex()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get('/collections');

        $response->assertStatus(200);
    }

    /**
     * Test getting specific collection
     *
     * @group collection
     */

    public function testGettingSpecificCollection()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->get('/collection/{id}');
        $response->assertStatus(200);
    }

    /**
     * Test deleting collection
     *
     * @group collection
     */
    public function testDeleteCollection()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $collection = factory(Collection::class)->create([
            'user_id' => $user->id,
        ]);

        $collection->users()->attach($user->id);

        $response = $this->delete("/web/collection/{$collection->id}");

        $response->assertJsonStructure([
            'data' => [
                'id',
            ],
        ])->assertStatus(201);
    }

    /**
     * Test creating a new collection
     *      * @group collection
     */

    public function testCreateCollection()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->post("/web/collections", [
            'label' => 'Test collection',
            'description' => 'this is a test collection',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'label',
                'description',
                'created_at',
                'updated_at',
            ],
        ])->assertStatus(201);
    }

    /**
     * Test requests for non-existent collections.
     *
     * @group collection
     */
    public function testGettingACollectionThatDoesNotExist()
    {
        $user = User::first();
        $this->actingAs($user);

        // Make up a weird id
        $id = 'ua700xf';

        $response = $this->get("/web/collection/{$id}");

        $response->assertStatus(404);
    }
}
