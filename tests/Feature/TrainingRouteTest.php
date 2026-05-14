<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_training_page_renders(): void
    {
        $this->get(route('training'))
            ->assertOk()
            ->assertSeeText('Learn a skill with Printbuka');
    }
}
