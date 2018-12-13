<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {
        
        $me = Auth::user();
        
        if($me->roles[0]->name != 'administrator') {
            return redirect()->guest(route('admin.dashboard'));
        }
        
        return view('admin.settings');
    }
    
}
