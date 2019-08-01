<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Auth\User\User;
use App\Models\Settings\Plan;
use App\Models\Settings\PlanXObject;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    private $DB;
    private $connection = "mysql";
    
    
    function forbidden() {
        return view('admin.forbidden', []);
    }
    
    
    function canIsee(User $me, int $objectId) {
        
        $validObj   = $objectId != 0 && $me->id != $objectId;
        $notAdmin   = $me->roles[0]->name != 'administrator';
        $permission = $this->checkPermission($me, $objectId);
        
        if ($validObj && !$permission && $notAdmin) {
            return response()->view('admin.forbidden');
        }
    }
    
    
    function checkPermission(User $me, int $objectId) {
        $users =  DB::select("SELECT distinct
                                    stores.*
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                WHERE (stores.id = $objectId OR storegroups.id = $objectId OR resellers.id = $objectId) 
                                   AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)");
        
        return isset($users[0]);
    }
    

    /**
     *  Filter Users
     *  
     * @author carlosferreira
     *  updated 05/13/2018
     *  $filterRole = The role to show. // 1 = admin, 2 = reseller, 3 = storegroup, 4 = store
     *  $parentId   = The Parent User filtered. Even if the actual user is an admin, this can be something.
     */
    public function filterUsers(Request $request = null, int $filterRole, int $parentId = null, $all = false, $ignorePaginator = false) {
        
        $me = Auth::user();
        
        $whereRole = (isset($filterRole) && $filterRole != 0) ? "users_roles.role_id = $filterRole" : "users_roles.role_id != 0" ;
        
        $filter = isset($request->filter) ? $request->filter : false;
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator' and !$filter) {
            $whereParentId = "";
            
        } else if (isset($parentId) and $parentId != 0) {
            $whereParentId = "AND (stores.parent_id = $parentId OR storegroups.parent_id = $parentId OR resellers.parent_id = $parentId)";
        }
        
        $whereSearch = "";
        if($filterRole == 4 && !empty($request->search)) {
            $search = str_replace("\'", "'", $request->search);
            $search = str_replace("'", "\'", $search);
            $whereSearch = "AND ( UPPER(stores.business_name) LIKE UPPER('%$search%') OR UPPER(stores.email) LIKE UPPER('%$search%') )";
        }
        
        // Applications
        $selectsApps    = "";
        $joinApps       = "";
        if($filterRole == 4) { // 1 = admin, 2 = reseller, 3 = storegroup, 4 = store
            $selectsApps    = " , apps.name as app_name ";
            $joinApps       = "LEFT JOIN store_app AS store_app ON store_app.store_guid = stores.store_guid
                                LEFT JOIN apps ON apps.guid = store_app.app_guid";
        }
        
        // Evironments
        $selectsEnvs    = "";
        $joinEnvs       = "";
        if($filterRole == 4) {
            $selectsEnvs    = " , environments.name as env_name ";
            $joinEnvs       = "LEFT JOIN store_environment AS store_env ON store_env.store_guid = stores.store_guid
                                LEFT JOIN environments ON environments.guid = store_env.environment_guid";
        }
        
        $orderBy = "";
        if(isset($_GET['sort'])) {
            $direction =  isset($_GET['order']) ? $_GET['order'] : ( isset($_GET['direction']) ? $_GET['direction'] : "ASC" );
            $orderBy   = "ORDER BY " . $_GET['sort'] . " " . $direction;
        }
        
        $users =  DB::select("SELECT distinct

                                    stores.*,
                                    users_roles.role_id
                                    $selectsApps
                                    $selectsEnvs

                                FROM users AS stores 
                                
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)

                                INNER JOIN users_roles ON users_roles.user_id = stores.id

                                $joinApps
                                $joinEnvs

                                WHERE (stores.deleted_at IS NULL OR stores.deleted_at = '') AND $whereRole $whereParentId $whereSearch

                                $orderBy");
        
        if ($request != null && !$ignorePaginator) {
            $amount = $all ? 1000 : 10;
            $users = $this->arrayPaginator($users, $request, $amount);
        }
        
        return $users;
    }
    
    
    public function getDevicesCount(bool $deleted = false) {
        
        $me = Auth::user();

        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
            
        } else if ($me->roles[0]->name == 'store') {
            $whereParentId = "AND (stores.id = $me->id)";
        }
        
        $whereDeleted = $deleted ? "AND is_deleted = 1" : "AND is_deleted = 0";
        
        $devices =  DB::select("SELECT count(devices.guid) AS count
                                FROM users AS stores
                                JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                JOIN devices ON devices.store_guid = stores.store_guid
                                JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereDeleted $whereParentId");
        
        return $devices[0]->count;
    }
    
    
    public function getActiveInactiveLicenses() {
        
        $me = Auth::user();
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
            
        } else if ($me->roles[0]->name == 'store') {
            $whereParentId = "AND (stores.id = $me->id)";
        }
        
        $licensesActive =  DB::select("SELECT 
                                    sum(case when devices.split_screen_parent_device_id = 0 then 1 else 0 end) AS active
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN devices ON devices.store_guid = stores.store_guid
                                INNER JOIN settings ON settings.store_guid = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId and devices.is_deleted <> 1");
        
        $licensesQuantity =  DB::select("SELECT 
                                	   sum(settings.licenses_quantity) AS quantity
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN settings ON settings.store_guid = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId");
        
        $active = 0;
        if (isset($licensesActive[0]->active)) {
            $active = $licensesActive[0]->active;
        }
        
        $quantity = 0;
        if (isset($licensesQuantity[0]->quantity)) {
            $quantity = $licensesQuantity[0]->quantity;
        }
        
        $inactive = $quantity - $active;
        
        $data = [
            'active'    => $active, 
            'inactive'  => $inactive
        ];
        
        return $data;
    }
    
    
    public function arrayPaginator($array, $request, $perPage)
    {
        $page = Input::get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        
        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
    
    
    public function getBasePlans() {
        
        $me = Auth::user();
        
        $plansXObjects = PlanXObject::where('user_id', '=', $me->id)->get();
        $guids = [];
        foreach($plansXObjects as $planXObject) {
            array_push($guids, $planXObject->plan_guid);
        }
        
        $plans = Plan::whereIn('guid', $guids);
        
        return $plans->where('delete_time', '=', 0)->orderBy('name')->get();
    }
    
    
    public function getMyPlanList() {
        
        $me = Auth::user();
        
        $adm = $me->hasRole('administrator');
        
        $plans = [];
        
        if($adm) {
            $plans = Plan::where(function ($query) use ($me) {
                $query->where('owner_id', '=', 0)->orWhere('owner_id', '=', $me->id);
            });
                
        } else {
            $plans = Plan::where('owner_id', '=', $me->id);
        }
        
        return $plans->where('delete_time', '=', 0)->orderBy('name')->get();
    }
    
    
    // Get System Apps
    public function getSystemApps() {
        $apps = DB::select("SELECT * FROM apps WHERE enable = 1 order by name");
        return isset($apps) ? $apps : [];
    }
    
    
    // Get Payment Type
    public function getPlanPaymentTypes() {
        $types = DB::select("SELECT * FROM payment_types WHERE status = 1 order by name");
        return isset($types) ? $types : [];
    }
    
    
    public function readableDatetime(int $datetime) {
        
        $timezone = isset(Auth::user()->timezone) ? Auth::user()->timezone : Vars::$timezoneDefault;
        
        $updateLast = new \DateTime();
        $updateLast = $updateLast->setTimezone(new \DateTimeZone($timezone));
        
        return $updateLast->setTimestamp($datetime)->format('D, d M Y H:i:s');
    }


    public function timezonesByCountry(Request $request) {
        if(empty($request->post('countryCode'))) {
            return [];
        }

        $countryCode = $request->post('countryCode');
        return DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);
    }

}







