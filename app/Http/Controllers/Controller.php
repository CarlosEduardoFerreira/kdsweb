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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    

    /**
     *  Filter Users
     *  
     * @author carlosferreira
     *  updated 05/13/2018
     *  $filterRole = The role to show.
     *  $parentId   = The Parent User filtered. Even if the actual user is an admin, this can be something.
     */
    public function filterUsers(Request $request = null, int $filterRole, int $parentId) {
        
        $me = Auth::user();
        
        $whereRole = (isset($filterRole) && $filterRole != 0) ? "users_roles.role_id = $filterRole" : "users_roles.role_id != 0" ;
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if (isset($parentId) and $parentId != 0) {
            $whereParentId = "AND (stores.parent_id = $parentId OR storegroups.parent_id = $parentId OR resellers.parent_id = $parentId)";
            
        } else if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
        }
        
        $orderBy = "";
        if(isset($_GET['sort']) && isset($_GET['order'])) {
            $orderBy = "ORDER BY " . $_GET['sort'] . " " . $_GET['order'];
        }
        
        $users =  DB::select("SELECT distinct
                                    stores.*,
                                    users_roles.role_id 
                                FROM users AS stores 
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE $whereRole $whereParentId
                                $orderBy");
        
        if ($request != null) {
            $users = $this->arrayPaginator($users, $request, 10);
        }
        
        return $users;
        
        
//         $wheres = ['users_roles.role_id' => $filterRole];
//         if(isset($parentId) and $parentId != 0) {
//             $wheres += ['users.parent_id' => $parentId];
//         }
        
//         $users = DB::table('users')
//                         ->join('users AS storegroups', 'storegroups.id', '=', 'users.parent_id')
//                         ->join('users_roles', 'users_roles.user_id', '=', 'users.id')
//                         ->where($wheres)
//                         ->paginate(10);
        
//         return $users;
        
//         $users = DB::table('users');
        
//         if(isset($parentId) and $parentId != 0) {
            
//             $wheres += ['users.parent_id' => $parentId];
            
//         } else {
            
//             if($filterRole == 3) { // storegroups
//                 $users = $users->where(['users.parent_id' => $me->id]);
                
//             } else if($filterRole == 4) { // stores
                
//                 $users = $users->join('users AS storegroups', 'storegroups.id', '=', 'users.parent_id');
//                 $users = $users->where(['storegroups.parent_id' => $me->id]);
//             }
//         }
        
//         $users = $users->join('users_roles', 'users_roles.user_id', '=', 'users.id');
//         $users = $users->where(['users_roles.role_id' => $filterRole]);
        
//         $users = $users->paginate(10);
    }
    
    
    public function getDevicesCount(bool $deleted = true) {
        
        $me = Auth::user();

        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
        }
        
        $whereDeleted = "";
        if ($deleted ) {
            $whereDeleted = "AND is_deleted_ = 1";
        }
        
        $devices =  DB::select("SELECT count(devices.guid_) AS count
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN devices ON devices.store_guid_ = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereDeleted $whereParentId");
        
        return $devices[0]->count;
    }
    
    
    public function getActiveInactiveLicenses() {
        
        $me = Auth::user();
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
        }
        
        $licensesActive =  DB::select("SELECT 
                                    sum(devices.login_) AS active
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN devices ON devices.store_guid_ = stores.store_guid
                                INNER JOIN settings ON settings.store_guid_ = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId");
        
        $licensesQuantity =  DB::select("SELECT 
                                	   sum(settings.licenses_quantity_) AS quantity
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN settings ON settings.store_guid_ = stores.store_guid
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







