<?php

namespace App\Http\Controllers\camps;

use App\Http\Controllers\Controller;
use App\Models\Camp;
use Illuminate\Http\Request;

class CampsController extends Controller
{
    public function index(){
        return Camp::where('id',2)->first()->companies[0]->camps[0]->companies;
    }
}
