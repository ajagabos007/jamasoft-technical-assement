<?php

namespace Tests\Feature\website;

use App\Models\User;
use App\Models\Website;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreateWebsiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User can create add favourite ebsite test
     *
     * @return void
     */
    public function test_user_can_add_favourite_a_website() : void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $this->actingAs($user);

        $website =Website::factory()->make();
        $response = $this->post(route('api.websites.store'), $website->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($website::class, $website->only($website->getFillable()));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

     /**
     * Admin can create a website test
     *
     * @return void
     */
    public function test_admin_can_create_a_website() : void
    {
        $this->actingAs($user = User::factory(['is_admin' => true])->withPersonalTeam()->create());

        $this->actingAs($user);

        $website =Website::factory()->make();
        $response = $this->post(route('api.websites.store'), $website->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($website::class, $website->only($website->getFillable()));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

}
