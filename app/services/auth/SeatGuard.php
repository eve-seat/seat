<?php

namespace App\Services\Auth;

class SeatGuard extends \Illuminate\Auth\Guard
{
    
    public function findGroupByName($group_name)
    {
        $group = \Group::where('name', '=', $group_name)->first();

        if($group)
            return $group;
        else
            return false;
    }

    public function findGroupByID($group_id)
    {
        $group = \Group::where('id', '=', $group_id)->first();

        if($group)
            return $group;
        else
            return false;
    }

    public function createGroup($info)
    {
        $group = new \Group();
        $group->name = $info['name'];

        if(isset($info['permissions']))
            $group->permissions = serialize($info['permissions']);

        $group->save();
        return $group;
    }

    public function deleteGroup($group)
    {
        $pivots = \GroupUserPivot::where('id', '=', $group->id)->delete();
        $group->delete();
    }

    public function addUserToGroup($user, $group)
    {
        $check = \GroupUserPivot::where('user_id', '=', $user->id)
            ->where('group_id', '=', $group->id)->first();

        if(!$check) {

            $user_group = new \SeatUserGroup();
            $user_group->user_id = $user->id;
            $user_group->group_id = $group->id;
            $user_group->save();
        }   
    }

    public function removeUserFromGroup($user, $group)
    {
        \GroupUserPivot::where('user_id', '=', $user->id)
            ->where('group_id', '=', $group->id)
            ->delete(); 
    }

    public function isSuperUser($user)
    {
        $groups = $user->groups;

        foreach($groups as $group) {

            $permissions = unserialize($group->permissions);

            if(array_key_exists('superuser', $permissions)) {

                if($permissions['superuser'] == 1)
                    return true;
            }
        }
        return false;
    }

    public function hasAccess($user, $permission)
    {
        if($this->isSuperUser($user))
            return true;
        
        $groups = $user->groups;

        $permissions = array();

        foreach($groups as $group) {

            array_merge($permissions, unserialize($group->permissions));
        }

        if(array_key_exists($permission, $permissions) OR array_key_exists('superuser', $permissions)) {

            if($permissions[$permission] == 1)
                return true;
        }
        return false;
    }

    public function findAllGroups()
    {
        $groups = \Group::all();
        return $groups;
    }

    public function getPermissions($group)
    {
        $permission_array = array();

        foreach(unserialize($group->permissions) as $key => $value) {

            if($value == 1)
                $permission_array[$key] = $value;
            
        }
        return $permission_array;
    }

    public function findAllUsersInGroup($group)
    {
        $pivot_info = \GroupUserPivot::where('group_id', '=', $group->id)->get();
        $users = array();

        foreach($pivot_info as $pivot) {

            array_push($users, \User::find($pivot->user_id));
        }
        return $users;
    }

}