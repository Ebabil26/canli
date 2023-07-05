<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;

use App\Http\Requests;

class TokenController extends Controller
{
    //

    public function add(Request $request) {

        $token = Token::firstOrCreate(['token'=> $request->token, 'type' => $request->type]);

        return response()->json($token);
    }
}
