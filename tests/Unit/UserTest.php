<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Cache;

class UserTest extends TestCase
{
  public function testRewardsReturnsDataInValidFormat()
  {
    $this->json('get', 'api/users/1/rewards?at=2022-05-20T12:00:00Z')
      ->assertStatus(Response::HTTP_OK)
      ->assertJsonStructure(
        [
          'data' => [
            '*' => [
              'availableAt',
              'expiresAt',
              'redeemedAt'
            ]
          ]
        ]
      );
  }
}
