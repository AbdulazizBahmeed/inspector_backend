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
        $questionsFormated = $questions->map(function ($question) {
            return $this->foramtQuestion($question);
        })->groupBy('type_name');

        return response()->json([
            'status' => true,
            'message' => 'retrieved the data',
            'data' => $questionsFormated,
            'camp_number' => $questions->isEmpty()? null:$questions[0],
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
        $validation = Validator::make(array_merge(['camp_id' =>$campId, 'day'=> $day], $request->all()), [
            'camp_id' => 'required|exists:camps,id',
            'day' => 'required|exists:batches,departure_day',
        ]);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors(),
            ], 400);
        }
        $answers =[];
        foreach ($request->answers as $key => $answer) {
            $answer = ['question_id' => strval($key), 'content' => $answer];
            $answers[] = $answer;
            $validatinRules = [
                'question_id' => 'required|exists:questions,id',
                'content' =>$answer['question_id'] !="5"?  'required|string':'required|image', 
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
            forEach($answers as  $answer){
                if($answer['question_id'] == "5"){
                    
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
