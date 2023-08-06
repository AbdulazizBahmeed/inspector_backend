<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(){
    $questions = Question::with('type')->get();
    $questions = $questions->map(function ($question){
        return $this->foramtQuestion($question);
    })->groupBy('type_name');

    return response()->json([
        'status' => true,
        'message' => 'retrieved the data',
        'data' => $questions,
    ], 200);
    }

    public function foramtQuestion($question){
        $result = [
            'question_id' => $question->id,
            'content' => $question->content,
            'type_id' => $question->type_id,
            'type_name' => $question->type->name,
        ];
        return $result;
    }
}
