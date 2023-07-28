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
        for ($x = 1; $x <= 5; $x++) {
            $data[] = [
                'id' => $x,
                'name' => 'cake number '.strval($x),
                'price' => $x * 5,
                'bakery' => 'sacharo',
                "describtion" => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.',
                'delivery_time' => 'delivery time '.strval($x + 1).' days',
                'rating' => 3.5,
                'image' => 'https://www.giftmyemotions.com/image/cache/floralnation/amazone/0121-800x800.jpg'
            ];
        }
        return response()->json([
            'status' => true,
            'message' => 'retrieved all the data successfully',
            'data' => $data,
        ], 200);
    }
}
