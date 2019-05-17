<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReportCostByPlanController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();

        $whereOwnerId = "AND plans.owner_id = 0";

        $sql = "SELECT
                    plans.name as planName,
                    plans.cost as planCost,
                    COUNT(devices.guid) as devicesTotal
                    
                FROM
                    plans
                    
                LEFT JOIN plans AS plansRes ON plansRes.base_plan = plans.guid AND plansRes.delete_time = 0
                LEFT JOIN plans AS plansStG ON plansStG.base_plan = plansRes.guid AND plansStG.delete_time = 0

                LEFT JOIN plans_x_objects AS plansXStr ON plansXStr.plan_guid  = plansStG.guid

                LEFT JOIN users AS stores ON stores.id = plansXStr.user_id

                LEFT JOIN devices ON devices.store_guid = stores.store_guid AND devices.is_deleted = 0
                LEFT JOIN users_roles ON users_roles.user_id = stores.id AND users_roles.role_id = 4
                
                WHERE
                    (stores.deleted_at IS NULL OR stores.deleted_at = '') 
                    AND plans.delete_time = 0
                    AND plans.owner_id = 0
                GROUP BY
                    plans.name, plans.cost";

        $stores =  DB::select($sql);
        
        return view('admin.reports.cost-by-plan', [ 'me' => $me, 'stores' => $stores ]);
    }

    
}