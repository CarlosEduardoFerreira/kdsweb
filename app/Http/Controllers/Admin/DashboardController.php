<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
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

        $stores_data = [];
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

            if (isset($user->store_guid)) {
                $store_name = isset($user->business_name) ? $user->business_name : $user->username;
                $stores_data[$user->store_guid] = $store_name;
            }

            natcasesort($stores_data);
        }

        $counts = [
            'resellers'   => $resellers,
            'storegroups' => $storegroups,
            'stores'      => $stores,
            'employees'   => $employees,
            'licenses'    => $licenses,
            'devices'     => $devices
        ];

        return view('admin.dashboard', ['counts' => $counts, 'me' => $me, 'stores' => $stores_data]);
    }

    
    public function getMainChartData(Request $request)
    {
        $phpDateFormat = "Y-m-d";
        $sqlDateFormat = "%Y-%m-%d";
        $data = [];
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $me = Auth::user();
        
        Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();
        
        // Switch environment based on Premium/Allee
        $isAppPremium = false;
        $isAdmin = $me->roles[0]->id == 1;
        $store_guid = DB::table("users")->where("id", "=", $me->id)->get()->first()->store_guid;
        
        $app_guid = DB::table("store_app")->where('store_guid', '=', $store_guid)->get()->first();     
        if (isset($app_guid->app_guid)) {
            $isAppPremium = ($app_guid->app_guid == "bc68f95c-1af5-47b1-a76b-e469f151ec3f");
        }
        
        // Populate data with 0
        $period = CarbonPeriod::create($request->get('start'), '1 day', $request->get('end'));
        foreach($period as $date) $data[strval($date->format('Y-m-d'))] = 0;
        $ans = [];

        // Is there a store guid to filter?
        $filter_store_guid = $request->store;

        if ($isAdmin) {
            // Admin: view non-deleted orders from all active stores
            $params = [$period->getStartDate()->startOfDay()->timestamp, 
                        $period->getEndDate()->endOfDay()->timestamp];

            if (strlen($filter_store_guid) > 0) {
                $addedCondition = " AND o.store_guid = ? ";
                $params[] = $filter_store_guid;
            } else {
                $addedCondition = "AND o.store_guid IN (SELECT store_guid 
                                    FROM {$mainDB}.users 
                                    WHERE active = 1) ";
            }

            $sql = "SELECT 
                        DATE_FORMAT(FROM_UNIXTIME(o.create_local_time), '$sqlDateFormat') AS orderDate, 
                        COUNT(1) AS total
                    FROM 
                        orders o
                    WHERE
                        o.create_local_time BETWEEN ? AND ?
                    AND
                        o.is_deleted = 0
                    $addedCondition
                    GROUP BY orderDate
                    ORDER BY orderDate ASC";

            

            $orders1 = DB::connection(env('DB_CONNECTION_PREMIUM', 'mysqlPremium'))->select($sql, $params);
            $orders2 = DB::connection(env('DB_CONNECTION', 'mysql'))->select($sql, $params);
            foreach ($orders1 as $order) $data[strval($order->orderDate)] = $order->total;
            foreach ($orders2 as $order) $data[strval($order->orderDate)] += $order->total;
        } else {
            $stores = [];
            $params = [$period->getStartDate()->startOfDay()->timestamp, 
                        $period->getEndDate()->modify("+1 day")->endOfDay()->timestamp];

            if (strlen($filter_store_guid) > 0) {
                $addedCondition = " AND o.store_guid = ? ";
                $params[] = $filter_store_guid;
            } else {
                $me_store_guid = Auth::user()->store_guid;
                $users = Controller::filterUsers(null, 0, 0);
                foreach ($users as $user) {
                    if ($user->role_id == 4) {
                        $stores[] = $user->store_guid;
                    }
                }
    
                # Include itself
                if (isset($me_store_guid)) {
                    if (!in_array($me_store_guid, $stores)) {
                        if (strlen($me_store_guid) > 0) {
                            $stores[] = $me_store_guid;
                        }
                    }
                }

                $addedCondition = "AND o.store_guid = '0'"; // Non-existent store
                if (count($stores) > 0) {
                    $question_marks = implode(',', array_fill(0, count($stores), '?'));
                    $addedCondition = "AND o.store_guid IN ($question_marks)";
                }
            }

            $connection = ($isAppPremium) ? env('DB_CONNECTION_PREMIUM', 'mysqlPremium') : env('DB_CONNECTION', 'mysql');
            $sql = "SELECT 
                        DATE_FORMAT(FROM_UNIXTIME(o.create_local_time), '$sqlDateFormat') AS orderDate, 
                        COUNT(1) AS total
                    FROM 
                        orders o
                    WHERE
                        o.create_local_time BETWEEN ? AND ?
                    AND
                        o.is_deleted = 0
                    $addedCondition
                    GROUP BY orderDate
                    ORDER BY orderDate ASC";

            foreach ($stores as $store) $params[] = $store;

            $orders = DB::connection($connection)->select($sql, $params);
            foreach ($orders as $order) $data[strval($order->orderDate)] = $order->total;
        }

        ksort($data);
        foreach ($data as $k => $v) $ans[] = [Carbon::createFromFormat($phpDateFormat, $k)->endOfDay()->getTimestamp() * 1000, $v];
       
        return response($ans);
    }

    public function getActiveInactiveLicensesGraph()
    {
        $licenses = Controller::getActiveInactiveLicenses();

        return response($licenses);
    }
}