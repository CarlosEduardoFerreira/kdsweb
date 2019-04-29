<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings\Plan;
use Illuminate\Support\Facades\Auth;


class PlanXResellerController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        if($me->roles[0]->name != 'administrator') {
            return redirect()->guest(route('admin.dashboard'));
        }
        
        $plans = Plan::where('delete_time', '=', 0)->orderBy('name')->get();
        
        $resellers = Controller::filterUsers(null, 2, $me->id);
        
        return view('admin.settings.plans-objects', [
            'plans' => $plans, 
            'objects' => $resellers,
            'objName' => 'Reseller'
        ]);
    }

    
}