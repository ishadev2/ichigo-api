<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

use App\Models\User;
use App\Models\Reward;

class TestController extends Controller
{
  public function __construct()
  {
    // Store some data in cache storage instead of DB
    if (!Cache::has('users')) {
      $users = collect([factory(User::class)->make()]); // just 1 user for testing purpose

      $tempRewards = [];

      for ($i = 1; $i <= 30; $i++) {
        // Manually generate entries for 2022-05 for testing purpose
        array_push($tempRewards, factory(Reward::class)->make(['availableAt' => '2022-05-' . sprintf("%02d", $i) . 'T00:00:00Z', 'expiresAt' => '2022-05-' . sprintf("%02d", ($i + 1)) . 'T00:00:00Z']));
      }

      $users[0]->rewards = collect($tempRewards);

      Cache::forever('users', $users);
    }
  }


  // Get rewards list
  public function rewards(Request $request, $userId)
  {
    // Validate the request
    $validator = Validator::make($request->all(), [
      'at' => 'required|date'
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => ["message" => $validator->errors()]]);
    }

    // Proceed if the user ID is available
    $user = Cache::get('users')->where('id', $userId)->first();

    if (!$user) {
      return response()->json(['error' => ["message" => "Given user ID is not found"]]);
    }

    // Find start and end of the given week day
    $givenDate = strtotime($request->get('at'));

    $weekStart = date('Y-m-d', strtotime('-' . date('w', $givenDate) . ' day', $givenDate)) . 'T00:00:00Z';
    $weekEnd = date('Y-m-d', strtotime('+' . (6 - date('w', $givenDate)) . ' day', $givenDate)) . 'T00:00:00Z';

    // Get rewards within the above week
    $rewards = $user->rewards->where('availableAt', '>=', $weekStart)->where('availableAt', '<=', $weekEnd);

    return response()->json(['data' => array_values($rewards->toArray())]);
  }


  // Redeem a reward
  public function redeem($userId, $date)
  {
    // Proceed if the user ID is available
    $user = Cache::get('users')->where('id', $userId)->first();

    if (!$user) {
      return response()->json(['error' => ["message" => "Given user ID is not found"]]);
    }

    // Get reward of given date
    $reward = $user->rewards->where('availableAt', $date)->where('redeemedAt', null)->toArray();

    if (count($reward)) {
      // Alternative way to update cached object as no database involved
      $index = key($reward);
      $reward = array_pop($reward);

      if ((strtotime($reward['expiresAt'])  <  strtotime(now()))) {
        return response()->json(['error' => ["message" => "This reward is already expired"]]);
      }

      // todo : in real scenario, redeeming before availableAt date should be blocked too

      $userData = Cache::get('users');
      $userData[0]->rewards[$index]->redeemedAt = now();
      Cache::forever('users', $userData);

      // Return updated entry details
      return response()->json(['data' => Cache::get('users')->where('id', $userId)->first()->rewards->where('availableAt', $date)->first()]);
    }

    return response()->json(['error' => ["message" => "No reward available or already redeemed for given date"]]);
  }
}
