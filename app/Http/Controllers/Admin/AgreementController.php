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

class AgreementController extends Controller
{
    
    public function agreement_page(User $reseller, Request $ip)
    {   
        $useremail = Auth::user()->email;
        $usersTable = DB::select( "SELECT * from agreement_acceptance WHERE email = ?", [$useremail]);
        $row = sizeof($usersTable);
        

      $id = Auth::user()->id;
      $ip = Request::ip();
      $page = 10;
      $time = DB::select("SELECT * FROM  kdsweb.agreement_acceptance WHERE email = ?", [$useremail]);
     
                            
                            foreach($time as $t)
                            {
                                $sig = sprintf("%08d", $id) . sprintf("%04d", $page) . md5($id . $ip . $page . $t->accepted_at);
                                
                            }
                            

        return view('admin.agreements.agreement_page', ['data' => $usersTable,'row' => $row, 'pdf' => $sig]);
    }


                            
}
