<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct()
    {
        //create read update delete
        $this->middleware(['permission:read_users'])->only('index');
        $this->middleware(['permission:create_users'])->only('create');
        $this->middleware(['permission:update_users'])->only('edit');
        $this->middleware(['permission:delete_users'])->only('destroy');

    }//end of constructor
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
     //   $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {
            $users = User::where(function ($q) use ($request) {
            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');

            });

        })->latest()->paginate(5);

        return view('dashboard.users.index', compact('users'));

    }//end of index

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.users.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
           // 'image' => 'image',
            'password' => 'required|confirmed',
            'permissions' => 'required|min:1'
        ]);
        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        if ($request->image) {

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();

        }//end of if
        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('dashboard.users.edit',compact('user'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',

            'permissions' => 'required|min:1'
        ]);

        $request_data = $request->except(['permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);

        $user->update($request_data);

        $user->syncPermissions($request->permissions);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->image != 'default.png') {

            Storage::disk('public_uploads')->delete('/user_images/' . $user->image);

        }//end of if

        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');
    }
}
