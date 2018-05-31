<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function filterUsers(int $filterRole, int $parentId) {
        $me = Auth::user();
        $myRole = $me->roles[0]->name;
        
        $wheres = ['users_roles.role_id' => $filterRole];
        if(isset($parentId) and $parentId != 0) {
            $wheres += ['users.parent_id' => $parentId];
        }
        
        $users = DB::table('users')
                    ->join('users_roles', 'users_roles.user_id', '=', 'users.id')
//                     ->leftJoin('users AS storegroups', 'storegroups.parent_id', '=', 'users.id')
//                     ->leftJoin('users AS stores', 'stores.parent_id', '=', 'storegroups.parent_id')
                    ->where($wheres)
                    ->paginate(10);
        
        return $users;
    }
    
}




