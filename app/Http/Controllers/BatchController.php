<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\Zone;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class BatchController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $zones = Zone::with(['camps' => function ($query) {
            $query->with(['batches.office.company', 'batches.camp']);
        }])->where('user_id', $user->id)->get();
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
                    $data['day ' . $batch->departure_day][] = $batch;
                }
            }
        }
        foreach ($data as $day => $batches) {
            $sortedBatches = collect($batches)->sortBy(function ($batch) {
                return $batch->departure_time;
            })->values();
            $data[$day] = $sortedBatches->map(function($batchData) use($day){
                return $this->formatBatchCard($batchData);
            });
        }
        return response()->json([
            'status' => true,
            'message' => 'retrieved the data successfully',
            'data' => $data
        ], 200);
    }
    public function show($campId, $day)
    {
        $validation = Validator::make(['camp_id' =>$campId, 'day'=> $day], [
            'camp_id' => 'exists:camps,id',
            'day' => 'exists:batches,departure_day',
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors(),
            ], 400);
        }
        $batches = Camp::with(['batches' => function ($query) use ($day) {
            $query->with(['camp','office.company', 'report'])->where('departure_day', $day);
        }])->where('id', $campId)->first()->batches;

        $data=[];
        $sortedBatches = $batches->sortBy('departure_time')->values();

        foreach ($sortedBatches as $batch) {
            $data[] = $this->formatBatchCard($batch);
        }

        return response()->json([
            'status' => true,
            'message' => 'retrieved the data successfully',
            'data' => $data,
            'camp_number' => $batches->isEmpty()? null:$batches[0]->camp->camp_label,
            'camp_upgraded_number' => $batches->isEmpty()? null:$batches[0]->camp->upgraded_camp_label,
        ], 200);
    }

    public function formatBatchCard($batch)
    {
        $result = [
            'camp_id' => $batch->camp->id,
            'batch_id' => $batch->id,
            'company_name' => $batch->office->company->name,
            'batch_name' => $batch->name,
            'office_number' => $batch->office->number,
            'departure_time' => $this->formatBatchTime($batch->departure_time),
            'prilgims_count' => $batch->pilgrims_count,
        ];
        return $result;
    }

    public function formatBatchTime($batchTime){
        $fullTime = date("h:i a", strtotime($batchTime));
        $time = substr($fullTime,0,5);
        $prefix = substr($fullTime,6) == "pm"? 'مساءً': 'صباحاً';
        return  sprintf("%s %s",$prefix,$time);;
    }
}