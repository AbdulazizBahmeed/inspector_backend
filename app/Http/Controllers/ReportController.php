<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\Question;
use Illuminate\Http\Request;
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
        $request->image->move(storage_path('images'),"myImage.png");
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
        $reportAnswers = [];

        foreach ($request->answers as $answer) {
            $validation = Validator::make($answer, [
                'question_id' => 'required|exists:questions,id',
                'answer' => 'exclude_if:question_id,4|required|string',
                'image' => 'exclude_unless:question_id,4|required|image',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validation->errors(),
                ], 400);
            }
            $reportAnswers[] = $answer;
        }


        return response()->json([
            'status' => true,
            'message' => 'received the data successfully',
            'data' => $reportAnswers
        ], 200);
    }
}
