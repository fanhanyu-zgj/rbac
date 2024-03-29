<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{   
    public function __construct(){
        $this->middleware(['auth','isAdmin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles=Role::all();
        return view('roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions=Permission::all();
        return view('roles.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|unique:roles|max:40',
            'permissions'=>'required',
        ]);
        $name=$request['name'];
        $role=new Role();
        $role->name=$name;
        $permissions=$request['permissions'];
        $role->save();
        foreach ($permissions as $permission){
            $p=Permission::where('id',$permission)->firstOrFail();
            $role=Role::where('name',$name)->first();
            $role->givePermissionTo($p);
        }
        return redirect()->route('roles.index')->with('flash_message','Role '.$role->name.' added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role=Role::findOrFail($id);
        $permissions=Permission::all();
        return view('roles.edit',compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role=Role::findOrFail($id);
        $this->validate($request,[
            'name'=>'required|unique:roles,name,'.$id,
            'permissions'=>'required',
        ]);
        $input=$request->except(['permission']);
        $permissions=$request['permissions'];
        $role->fill($input)->save();
        $p_all=Permission::all();
        foreach($p_all as $p){
            $role->revokePermissionTo($p);//移除角色关联的所有权限
        }
        foreach($permissions as $permission){
            $p=Permission::where('id',$permission)->firstOrFail();
            $role->givePermissionTo($p);//分配权限到角色
        }
        return redirect()->route('roles.index')->with('flash_message','Role '.$role->name.' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role=Role::findOrFail($id);
        $role->delete();
        return redirect('roles.index')->with('flash_message','Role  deleted!');
    }
}
