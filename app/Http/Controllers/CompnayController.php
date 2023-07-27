<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\Company;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;

class CompnayController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'retrieved all the data successfully',
            'data' => Company::all(),
        ], 200);
    }
}
