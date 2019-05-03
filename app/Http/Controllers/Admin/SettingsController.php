<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class SettingsController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        $plansCount = count($this->getMyPlanList());
        
        return view('admin.settings', ['me' => $me, 'plansCount' => $plansCount]);
    }

    
}












