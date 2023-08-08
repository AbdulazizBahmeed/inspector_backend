<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\Question;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index()
    {
        $questions = Question::with('type')->get();
        $questions = $questions->map(function ($question) {
            return $this->foramtQuestion($question);
        })->groupBy('type_name');

        return response()->json([
            'status' => true,
            'message' => 'retrieved the data',
            'data' => $questions,
        ], 200);
    }

    public function foramtQuestion($question)
    {
        $result = [
            'question_id' => $question->id,
            'content' => $question->content,
            'type_name' => $question->type->name,
        ];
        return $result;
    }

    public function store(Request $request, $campId, $day)
    {
        $minmumAnswers =Question::where("optional", 'false')->count();
        $validation = Validator::make(array_merge(['camp_id' =>$campId, 'day'=> $day], $request->all()), [
            'camp_id' => 'required|exists:camps,id',
            'day' => 'required|exists:batches,departure_day',
            'answers' => 'required|array|min:'.$minmumAnswers,
            // 'departure_time' => 'required|date',
        ]);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors(),
            ], 400);
        }

        foreach ($request->answers as $answer) {
            $validatinRules = [
                'question_id' => 'required|exists:questions,id',
                'content' =>$answer['question_id'] !=4?  'required|string':'required|image', 
            ];
            $validation = Validator::make($answer, $validatinRules);

            if ($validation->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validation->errors(),
                ], 400);
            }
        }

        $batches = Camp::with(['batches' => function ($query) use ($day) {
            $query->whereDoesntHave('report')->where('departure_day', $day);
        }])->where('id', $campId)->first()->batches->sortBy('departure_time')->values();

        $reportedBatch = $batches->first();
        if($reportedBatch){
            $report = Report::create([
                "departure_time" => Carbon::now(),
                "batch_id" => $reportedBatch->id
            ]);

            $imageCounter = 1;
            forEach($request->answers as  $answer){
                if($answer['question_id'] == 4){
                    $fileName = 'report'.$report->id.',imageNumber'.$imageCounter.'.'.$answer['content']->extension();
                    $answer['content']->move(public_path('storage'), $fileName);
                    $answer['content'] = $fileName;
                    $imageCounter++;
                }
                Answer::create([
                    'content' => $answer['content'],
                    'question_id' => $answer["question_id"],
                    'report_id' => $report->id,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'received the data successfully',
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'there is no batches left for this camp and day',
            ], 400);
        }
    }
}
