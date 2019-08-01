<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReportCostByStatementController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }
    
    
    public function index() {
        $me = Auth::user();
        
        $stores = $this->getStoresForStatements($me, new Request());
        
        return view('admin.reports.cost-by-statement', [ 'me' => $me, 'stores' => $stores ]);
    }
    
    
    public static function getStoresForStatements($me, Request $request) {
        
        $liveStoreGuid = 'b78ba4b7-6534-4e3e-87a5-ee496b1b4264';
        
        $role = $me->roles[0]->name;
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id)";
        if ($role == 'administrator') {
            $whereParentId = "AND (resellers.parent_id = $me->id OR resellers.parent_id = 0)";
        }
        
        $searchSQL = "";
        if(!empty($request->search)) {
            $searchSQL = "WHERE allStores.resellerBName LIKE '%$request->search%'
                            OR  allStores.storegroupBName LIKE '%$request->search%'
                            OR  allStores.storeBName LIKE '%$request->search%'
                            OR  allStores.planName LIKE '%$request->search%'";
        }
        
        $sql = "SELECT
                    allStores.store_guid,
                    allStores.resellerBName,
                    allStores.storegroupBName,
                    allStores.storeBName,
                    allStores.planName,
                    allStores.planCost,
                    allStores.live
                FROM
                    (SELECT
                         stores.store_guid as store_guid,
                         resellers.business_name as resellerBName,
                         storegroups.business_name as storegroupBName,
                         stores.business_name as storeBName,
                         
                         (case when '$role' = 'storegroup' then plansStG.name
                         else
                            case when '$role' = 'reseller' then plansRes.name
                            else
                                plansAdm.name
                            end
                         end) as planName,
                         
                         (case when '$role' = 'storegroup' then plansStG.cost
                         else
                            case when '$role' = 'reseller' then plansRes.cost
                            else
                                plansAdm.cost
                            end
                         end) as planCost,
                         
                        (case when store_environment.environment_guid = '$liveStoreGuid' then true else false end) as live
                        
                    FROM
                         users AS stores
                         
                        JOIN users AS storegroups ON storegroups.id = stores.parent_id
                        JOIN users AS resellers ON resellers.id = storegroups.parent_id
                        
                        LEFT JOIN plans_x_objects AS plansXStr ON plansXStr.user_id = stores.id
                        
                        LEFT JOIN plans AS plansStG ON plansStG.guid = plansXStr.plan_guid AND plansStG.delete_time = 0
                        LEFT JOIN plans AS plansRes ON plansRes.guid = plansStG.base_plan  AND plansRes.delete_time = 0
                        LEFT JOIN plans AS plansAdm ON plansAdm.guid = plansRes.base_plan  AND plansAdm.delete_time = 0
                        
                        LEFT JOIN devices ON devices.store_guid = stores.store_guid AND devices.is_deleted = 0
                        LEFT JOIN users_roles ON users_roles.user_id = stores.id AND users_roles.role_id = 4
                        LEFT JOIN store_environment ON store_environment.store_guid = stores.store_guid
                        
                    WHERE
                         (stores.deleted_at IS NULL OR stores.deleted_at = '') $whereParentId
                    GROUP BY
                        stores.store_guid,
                        resellers.business_name,
                        storegroups.business_name,
                        stores.business_name,
                        
                        (case when '$role' = 'storegroup' then plansStG.name
                         else
                            case when '$role' = 'reseller' then plansRes.name
                            else
                                plansAdm.name
                            end
                         end),
                         
                        (case when '$role' = 'storegroup' then plansStG.cost
                         else
                            case when '$role' = 'reseller' then plansRes.cost
                            else
                                plansAdm.cost
                            end
                         end),
                         
                        store_environment.environment_guid
                    ORDER BY
                        resellers.business_name) AS allStores $searchSQL";
        
        return  DB::select($sql);
    }

    
}












