<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        // $zones = $request->user()->zones;
        $user = $request->user();
        $zones = Zone::with("camps.batches.office.company")->where('user_id', $user->id)->get();

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
                    $companyName = $batch->office->company->name;
                    // $batchData = $batch;
                    // return gettype($companyName);
                    // $batchData->push($companyName);
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
