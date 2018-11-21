<?php

namespace App\Http\Controllers;

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


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    
    function __construct() {
        
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
        //return false;
    }
    
    
    function forbidden() {
        return view('admin.forbidden', []);
    }
    

    /**
     *  Filter Users
     *  
     * @author carlosferreira
     *  updated 05/13/2018
     *  $filterRole = The role to show.
     *  $parentId   = The Parent User filtered. Even if the actual user is an admin, this can be something.
     */
    public function filterUsers(Request $request = null, int $filterRole, int $parentId = null) {
        
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
        if($filterRole == 4 && $request->search != null) {
            $whereSearch = "AND ( UPPER(stores.business_name) LIKE UPPER('%$request->search%') OR UPPER(stores.email) LIKE UPPER('%$request->search%') )";
        }
        
        $orderBy = "";
        if(isset($_GET['sort'])) {
            $direction =  isset($_GET['order']) ? $_GET['order'] : ( isset($_GET['direction']) ? $_GET['direction'] : "ASC" );
            $orderBy   = "ORDER BY " . $_GET['sort'] . " " . $direction;
        }
        
        $users =  DB::select("SELECT distinct
                                    stores.*,
                                    users_roles.role_id 
                                FROM users AS stores 
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE $whereRole $whereParentId $whereSearch
                                $orderBy");
        
        if ($request != null) {
            $users = $this->arrayPaginator($users, $request, 10);
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
                                    sum(case when devices.split_screen_parent_device_id = 0 then devices.license else 0 end) AS active
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN devices ON devices.store_guid = stores.store_guid
                                INNER JOIN settings ON settings.store_guid = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId");
        
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
    
}







