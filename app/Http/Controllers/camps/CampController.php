<?php

namespace App\Http\Controllers\camps;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class CampController extends Controller
{
    public function getAllCamps(Request $request)
    {
        
    }

    public function getAllBatches(Request $request)
    {
        $zones = $request->user()->zones;
        $data = [
            'day 9' => [],
            'day 10' => [],
            'day 11' => [],
            'day 12' => [],
            'day 13' => [],
        ];
        foreach ($zones as $zone) {
            foreach ($zone->camps as $camp) {
                foreach ($camp->batches as $batch) {
                    $data['day ' . $batch->deaprture_day][] = $batch;
                }
            }
        }
        foreach ($data as $day => $batches) {
            $data[$day] = collect($batches)->sortBy('departure_time')->flatten();
        }
        return response()->json([
            'status' => true,
            'message' => 'retrieved the data successfully',
            'data' => $data
        ], 200);
    }
}
