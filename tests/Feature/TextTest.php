<?php

namespace Tests\Feature;

use App\Models\Text;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        Text::factory()->count(10)->create();

        $response = $this->getJson('/api/texts');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['key', 'value']]])
            ->assertJsonCount(10, 'data');
    }
}
