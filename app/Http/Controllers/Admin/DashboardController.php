<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#use App\Models\Auth\User\User;
use App\Models\Order;
use Arcanedev\LogViewer\LogViewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

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

        // $dataArray = [];
        // $period = CarbonPeriod::create($request->get('start'), '1 month', $request->get('end'));
        // foreach($period as $date) {
        //     $dataArray[$date->format("Y-m")] = ["display" => $date->format("m/Y"), "value" => 0];
        // }

        // $orders = Order::where("is_deleted", 0)
        //                 ->whereBetween("create_local_time", [$period->getStartDate()->timestamp, 
        //                                                      $period->getEndDate()->timestamp])
        //                 ->take(10)->get();


        // ksort($dataArray);

        // foreach ($orders as $order) {
        //     $create_time = Carbon::createFromTimestamp($order->create_local_time);
        //     $dataArray[$create_time->format("Y-m")]["value"] += 1;
        // }

        // $result = [];
        // foreach ($dataArray as $data) {
        //     $result[] = [$data["display"], $data["value"]];
        // }


                    $start = new Carbon($request->get('start'));
                    $end = new Carbon($request->get('end'));

                    $dates = collect(LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
                        $value = new Carbon($value);
                        return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
                    });


                    $levels = LogViewer::levels();

                    $data = [];

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



        return response($result);
    }

    public function getActiveInactiveLicensesGraph()
    {
        $licenses = Controller::getActiveInactiveLicenses();

        return response($licenses);
    }
}
