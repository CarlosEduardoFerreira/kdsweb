<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings\Plan;
use Illuminate\Support\Facades\Auth;


class PlanXStoreGroupController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        $plans = Plan::where('delete_time', '=', 0)->orderBy('name')->get();
        
        $storegroups = Controller::filterUsers(null, 3, $me->id);
        
        return view('admin.settings.plans-objects', [
            'plans' => $plans,
            'objects' => $storegroups,
            'objName' => 'Store Group'
        ]);
    }

    
}