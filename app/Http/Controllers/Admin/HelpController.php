<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User\User;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use App\Models\Auth\Role\Role;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class HelpController extends Controller
{
    //
    
    public function help_page()
    {

          
    return view('admin.help.help_page');
    }
}
