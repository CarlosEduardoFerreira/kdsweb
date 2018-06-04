<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LocationController extends Controller
{
    
    public function getCountryList(Request $request)
    {
        $countries = DB::select("select * from countries order by name");
        return response()->json($countries);
    }
    
    public function getStateList(Request $request)
    {
        $states = DB::select("select id, name from states where country_id = " . $request->country_id . " order by name");
        return response()->json($states);
    }
    
    public function getCityList(Request $request)
    {
        $cities = DB::select("select id, name from cities where state_id = " . $request->state_id . " order by name");
        return response()->json($cities);
    }
    
}
?>