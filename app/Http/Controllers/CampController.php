<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class CampController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $zones = Zone::with(['camps.batches'])->where('user_id', $user->id)->get();

        $data = [
            'day 9' => collect(),
            'day 10' => collect(),
            'day 11' => collect(),
            'day 12' => collect(),
            'day 13' => collect(),
        ];
        foreach ($zones as $zone) {
            foreach ($zone->camps as $camp) {
                foreach($camp->batches as $batch){
                    if(!$data['day '.$batch->deaprture_day]->contains('id', $camp->id)){
                        $data['day '.$batch->deaprture_day]->push($camp);
                    }
                }
            }
        }
        foreach ($data as $day => $camps) {
            $campsSorted =  $camps->sortBy(function($camp){
                return $camp->batches->min('departure_time');
            })->values();
            $data[$day] = $campsSorted->map(function($campData){
                return $this->formatCampCard($campData);
            });
        }
        return response()->json([
            'status' => true,
            'message' => 'retrieved the data successfully',
            'data' => $data
        ], 200);
    }

    public function formatCampCard($camp)
    {
        $result = [
            'camp_id' => $camp->id,
            'camp_number' => $camp->upgraded_camp_label? $camp->upgraded_camp_label:$camp->camp_label,
            'batches_count'=> $camp->batches->count()
        ];
        return $result;
    }
}
