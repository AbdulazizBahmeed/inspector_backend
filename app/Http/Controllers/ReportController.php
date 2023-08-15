<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Batch;
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
        $answers =[];
        foreach ($request->all() as $key => $answer) {
            $answersObject['question_id'] = strval($key);
            $answersObject['content'] = $answer;
            if($answersObject['question_id'] == "4" ) $answersObject['image'] = $answer;

            $answers["answers"][] = $answersObject;
        }

        $validation = Validator::make(array_merge(['camp_id' =>$campId, 'day'=> $day], $answers), [
            'camp_id' => 'required|exists:camps,id',
            'day' => 'required|exists:batches,departure_day',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.content' =>"exclude_if:answers.*.question_id,4|string",
            'answers.*.image' =>"exclude_unless:answers.*.question_id,4|image"
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors(),
            ], 400);
        }
        $inputs =$validation->validated();
        $reportedBatch = Batch::where('departure_day', $inputs["day"])->where("camp_id",$inputs["camp_id"])->get()->sortBy('departure_time')->first();
        if($reportedBatch){
            $report = Report::create([
                "departure_time" => Carbon::now(),
                "batch_id" => $reportedBatch->id
            ]);

            $imageCounter = 1;
            forEach($inputs["answers"] as  $answer){
                if($answer['question_id'] == "4"){
                    $fileName = 'report'.$report->id.',imageNumber'.$imageCounter.'.'.$answer['image']->extension();
                    $answer['image']->move(public_path('storage'), $fileName);
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
