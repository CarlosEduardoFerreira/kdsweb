<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Breadcrumbs;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User\User;

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
        
        $users = User::with('roles')->sortable(['email' => 'asc'])->paginate();
        
        $i = 0;
        foreach ($users as $user) {
            
            // remove if is not the specific role (Store Group)
            if ($user->roles[0]["id"] != $filterRole) {
                unset($users[$i]);
            }
            
//             // if is not admin
//             if ($myRole != 'administrator') {
//                 // remove if is not my child
//                 //echo "<br>debug 1: " . $user->parent_id  . "|" . $me->id;
//                 if ($user->parent_id != $me->id) {
//                     unset($users[$i]);
//                 }
//             }
            
            // if it should filter
            if (isset($parentId) && $parentId != 0) {
                //echo "<br>debug 2: " . $user->parent_id . "|" . $parentId . "|";
                if ($user->parent_id != $parentId) {
                    unset($users[$i]);
                }
            } else if ($myRole != 'administrator') {
                // remove if is not my child
                //echo "<br>debug 1: " . $user->parent_id  . "|" . $me->id;
                if ($user->parent_id != $me->id) {
                    unset($users[$i]);
                }
            }
            
            $i++;
        }
        
        return $users;
    }
    
}
