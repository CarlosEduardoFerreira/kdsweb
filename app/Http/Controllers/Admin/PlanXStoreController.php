<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings\Plan;
use Illuminate\Support\Facades\Auth;


class PlanXStoreController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        $plans = $this->getMyPlanList();
        
        $stores = Controller::filterUsers(null, 4, $me->id);
        
        return view('admin.settings.plans-objects', [
            'plans' => $plans,
            'objects' => $stores,
            'objName' => 'Store'
        ]);
    }

    
}