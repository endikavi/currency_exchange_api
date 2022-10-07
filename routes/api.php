<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Currency;
use App\Models\Quote;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/// Currencies CRUD ///

//returns a JSON representation of the currency model
Route::get('/currencies/{currency_code}', function ($currency_code) {
    $currency = Currency::where('code',$currency_code)->first();
    //check if currency exists else return 404
    if($currency){
        response()->json($currency, 200);
    }else{
        return response()->json(['message' => 'Currency not found'], 404);
    }
});

//creates a new currency based on the `code` and `name` parameters sent in the request
Route::post('/currencies', function (Request $request) {
    //check if the request has the required parameters
    if ($request->has('code') && $request->has('name')) {
        //check if the currency already exists
        $currency = Currency::where('code',$request->code)->first();
        if($currency){
            return response()->json(['message' => 'Currency already exists'], 409);
        }else{
            //create the currency
            $currency = Currency::create([
                'code' => $request->code,
                'name' => $request->name,
            ]);
            return response()->json(['message' => 'Currency created successfully'], 201);
        }
    }else{
        return response()->json(['message' => 'Missing required parameters'], 400);
    }
});

//updates a currency `code` and `name` based on the parameters sent in the request.
Route::put('/currencies/{currency_code}', function () {
    $currency = Currency::where('code',$currency_code)->first();
    //check if currency exists else return 404
    if($currency){
        if ($request->has('code')) {
            //check if the currency already exists
            $currency = Currency::where('code',$request->code)->first();
            if($currency){
                return response()->json(['message' => 'Currency already exists'], 409);
            }else{
                //update the currency code
                $currency->code = $request->code;
            }
        }
        if ($request->has('name')) {
            //update the currency name
            $currency->name = $request->name;
        }
        $currency->save();
        return response()->json(['message' => 'Currency updated successfully'], 200);
    }else{
        return response()->json(['message' => 'Currency not found'], 404);
    }
    
});

//deletes a currency from the system.
Route::delete('/currencies/{currency_code}', function () {
    $currency = Currency::where('code',$currency_code)->first();
    //check if currency exists else return 404
    if($currency){
        $currency->delete();
        return response()->json(['message' => 'Currency deleted successfully'], 200);
    }else{
        return response()->json(['message' => 'Currency not found'], 404);
    }
});

/// Quotes ///

//returns the latest quote from a combination of currencies.
Route::get('/latest/{from}/{to}', function ($from, $to) {
    //check if the currencies exist
    $from_currency = Currency::where('code',$from)->first();
    $to_currency = Currency::where('code',$to)->first();
    if($from_currency && $to_currency){
        $value = apilayerConvert($from_currency->code, $to_currency->code);
        if($value){
            //check if the quote already exists
            $quote = Quote::where('from_currency',$from_currency->id)->where('to_currency',$to_currency->id)->first();
            if($quote){
                //update the quote
                $quote->value = $value;
                $quote->save();
            }else{
                //create the quote
                $quote = Quote::create([
                    'from_currency' => $from_currency->id,
                    'to_currency' => $to_currency->id,
                    'value' => $value,
                ]);
            }
            return response()->json([
                'id' => $quote->id,
                'from_currency' => $from_currency,
                'to_currency' => $to_currency,
                'value' => $value,
                'created_at' => $quote->created_at,
                'updated_at' => $quote->updated_at,
            ], 200);
        }else{
            return response()->json(['message' => 'Error getting quote'], 500);
        }
        
    }else{
        return response()->json(['message' => 'Currency not found'], 404);
    }
});

//returns the latests quotes for all combination of currencies
Route::get('/latest', function () {
    $quotes = Quote::all();
    //get relations
    foreach($quotes as $quote){
         $quote->from_currency = Currency::find($quote->from_currency);
         $quote->to_currency = Currency::find($quote->to_currency);
    }
    return response()->json($quotes, 200);
});