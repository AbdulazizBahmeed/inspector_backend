<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
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
                    $data['day '.$batch->departure_day][] = $this->formatBatchCard($batch);
                }
            }
        }
        foreach ($data as $day => $batches) {
            $data[$day] = collect($batches)->sortBy('departure_time')->values();
        }
        return response()->json([
            'status' => true,
            'message' => 'retrieved the data successfully',
            'data' => $data
        ], 200);
    }

    public function formatBatchCard($batch){
        $result = [
            'batch_id' => $batch->id,
            'company_name' => $batch->office->company->name,
            'batch_name' => $batch->name,
            'office_number' => $batch->office->number,
            'departure_time' => $batch->departure_time,
            'prilgims_count' => $batch->pilgrims_count
        ];
        return $result;
    }
}
