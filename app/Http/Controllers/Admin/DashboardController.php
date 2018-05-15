<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\User\User;
use App\Models\Auth\Role\Role;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $me = Auth::user();

        $users = User::with('roles')->sortable(['email' => 'asc'])->paginate();

//          Roles -------------------------------------
//          id  name            weight     role
//          1   administrator   1000    -> Admin
//          2   reseller         900    -> Reseller
//          3   controller       800    -> Store Group
//          4   manager          700    -> Store
//          5   employee           1    -> Employee
//          7   authenticated      0    -> Undefined


        //echo "count: " . count(array($users));

        $resellers   = 0;
        $storegroups = 0;
        $stores      = 0;
        $employees   = 0;
        $licenses    = 0;
        $devices     = 0;

        foreach ($users as $user) {
            if ($user->roles[0]["id"] == 2) {
                $resellers++;

            } else if ($user->roles[0]["id"] == 3) {
                $storegroups++;

            } else if ($user->roles[0]["id"] == 4) {
                $stores++;

            } else if ($user->roles[0]["id"] == 5) {
                $employees++;

            }
        }

        $counts = [
            'resellers'   => $resellers,
            'storegroups' => $storegroups,
            'stores'      => $stores,
            'employees'   => $employees,
            'licenses'    => $licenses,
            'devices'     => $devices
        ];

        return view('admin.dashboard', ['counts' => $counts, 'me' => $me]);
    }


    public function getLogChartData(Request $request)
    {
        \Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));

        $dates = collect(\LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
            $value = new Carbon($value);
            return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
        });


        $levels = \LogViewer::levels();

        $data = [];

        while ($start->diffInDays($end, false) >= 0) {

            foreach ($levels as $level) {
                $data[$level][$start->format('Y-m-d')] = 0;
            }

            if ($dates->contains($start->format('Y-m-d'))) {
                /** @var  $log Log */
                $logs = \LogViewer::get($start->format('Y-m-d'));

                /** @var  $log LogEntry */
                foreach ($logs->entries() as $log) {
                    $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
                }
            }

            $start->addDay();
        }

        return response($data);
    }

    public function getRegistrationChartData()
    {

        $data = [
            'registration_form' => User::whereDoesntHave('providers')->count(),
            'google' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'google');
            })->count(),
            'facebook' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'facebook');
            })->count(),
            'twitter' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'twitter');
            })->count(),
        ];

        return response($data);
    }
}
