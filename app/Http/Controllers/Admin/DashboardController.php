<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

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

    
    public function getMainChartData(Request $request)
    {
        $phpDateFormat = "Y-m-d";
        $sqlDateFormat = "%Y-%m-%d";
        
        Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $data = [];
        $period = CarbonPeriod::create($request->get('start'), '1 day', $request->get('end'));
        foreach($period as $date) {
            $data[$date->format($phpDateFormat)] = 0;
        }

        $me = Auth::user();

        if ($me->roles[0]->id == 1) {
            // Admin: view non-deleted orders from all active stores
            $sql = "SELECT 
                        DATE_FORMAT(FROM_UNIXTIME(create_local_time), '$sqlDateFormat') AS orderDate, 
                        COUNT(1) AS total
                    FROM orders o
                    WHERE
                        create_local_time BETWEEN ? AND ?
                    AND
                        is_deleted = 0
                    AND
                        o.store_guid IN (SELECT store_guid 
                                            FROM kdsweb.users 
                                            WHERE active = 1)
                    GROUP BY orderDate
                    ORDER BY orderDate DESC";

            $params = [$period->getStartDate()->timestamp, 
                        $period->getEndDate()->timestamp];
        } else {
            $sql = "SELECT 
                        DATE_FORMAT(FROM_UNIXTIME(create_local_time), '$sqlDateFormat') AS orderDate, 
                        COUNT(1) AS total
                    FROM orders o
                    WHERE
                        create_local_time BETWEEN ? AND ?
                    AND
                        is_deleted = 0
                    AND
                        o.store_guid IN (SELECT store_guid 
                                            FROM kdsweb.users 
                                            WHERE (id = ? OR parent_id = ?)
                                            AND active = 1)
                    GROUP BY orderDate
                    ORDER BY orderDate DESC";

            $params = [$period->getStartDate()->timestamp, 
                $period->getEndDate()->timestamp, 
                $me->id, 
                $me->id];
        }

        $orders = DB::select($sql, $params);

        ksort($data);

        foreach ($orders as $order) {
            $data[$order->orderDate] = $order->total;
        }

        $result = ["data" => []];
        foreach ($data as $key => $value) {
            $result["data"][$key] = $value;
        }

        return response($result);
    }

    public function getActiveInactiveLicensesGraph()
    {
        $licenses = Controller::getActiveInactiveLicenses();

        return response($licenses);
    }
}
