<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Arcanedev\LogViewer\Facades\LogViewer;
use Carbon\Carbon;

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
     * -- Roles ----------------------------------------
          id  name            weight     role
          1   administrator   1000    -> Admin
          2   reseller         900    -> Reseller
          3   storegroup       800    -> Store Group
          4   stores           700    -> Store
          5   employee           1    -> Employee
          7   authenticated      0    -> Undefined
     * -------------------------------------------------
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $me = Auth::user();
        
        $users = Controller::filterUsers(null, 0, 0);

        //echo "count users: " . count($users);

        $resellers   = 0;
        $storegroups = 0;
        $stores      = 0;
        $employees   = 0;
        $licenses    = 0;
        $devices     = Controller::getDevicesCount();

        foreach ($users as $user) {
            if ($user->role_id == 2) {
                $resellers++;

            } else if ($user->role_id == 3) {
                $storegroups++;

            } else if ($user->role_id == 4) {
                $stores++;

            } else if ($user->role_id == 5) {
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


    // public function getLogChartData(Request $request)
    // {
    //     Validator::make($request->all(), [
    //         'start' => 'required|date|before_or_equal:now',
    //         'end' => 'required|date|after_or_equal:start',
    //     ])->validate();

    //     $start = new Carbon($request->get('start'));
    //     $end = new Carbon($request->get('end'));

    //     $dates = collect(LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
    //         $value = new Carbon($value);
    //         return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
    //     });


    //     $levels = LogViewer::levels();

    //     $data = [];

    //     while ($start->diffInDays($end, false) >= 0) {

    //         foreach ($levels as $level) {
    //             $data[$level][$start->format('Y-m-d')] = 0;
    //         }

    //         if ($dates->contains($start->format('Y-m-d'))) {
    //             /** @var  $log Log */
    //             $logs = LogViewer::get($start->format('Y-m-d'));

    //             /** @var  $log LogEntry */
    //             foreach ($logs->entries() as $log) {
    //                 $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
    //             }
    //         }

    //         $start->addDay();
    //     }

    //     return response($data);
    // }
    
    public function getLogChartData(Request $request)
    {
        Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $mainDB = env('DB_DATABASE', 'kdsweb');

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));

        $sql = "SELECT create_time, COUNT(guid)
                FROM {$mainDB}.orders
                WHERE is_deleted = 0
                AND create_time BETWEEN {$start->timestamp} AND {$end->timestamp}
                GROUP BY create_time";

        $data = [];
        $dbData = DB::select($sql);
        if (!isset($dbData)) return $data;
        
        $dates = collect(LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
            $value = new Carbon($value);
            return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
        });

        $levels = LogViewer::levels();
        while ($start->diffInDays($end, false) >= 0) {

            foreach ($levels as $level) {
                $data[$level][$start->format('Y-m-d')] = 0;
            }

            if ($dates->contains($start->format('Y-m-d'))) {
                /** @var  $log Log */
                $logs = LogViewer::get($start->format('Y-m-d'));

                /** @var  $log LogEntry */
                foreach ($logs->entries() as $log) {
                    $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
                }
            }

            $start->addDay();
        }

        return response($data);
    }

    public function getActiveInactiveLicensesGraph()
    {
        $licenses = Controller::getActiveInactiveLicenses();

        return response($licenses);
    }
}
