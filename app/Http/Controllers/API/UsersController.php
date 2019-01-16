<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize("isAdmin");
        if(Gate::allows("isAdmin") || Gate::allows("isAuthor") )
        {
            return User::latest()->where("deleted",0)->paginate(5);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return ["message"=>"worked"];
        // return $request->all();
        $validateData = $request->validate([
            "name"=>"required|string|max:191",
            "email"=>"required|unique:users|email|max:191",
            "password"=>"required|confirmed|string|min:6",
            "type"=>"required|string|max:191"
        ]);

        return User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>Hash::make($request->password),
            "type"=>$request->type,
            "bio"=>$request->bio
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth("api")->user();
    }

    public function updateProfile(Request $request)
    {
        $validateData = $request->validate([
            "name"=>"required|string|max:191",
            "email"=>"required|email|max:191,unique:users,email,".auth("api")->user()->id,
            // "password"=>"required|confirmed|string|min:6",
            "type"=>"required|string|max:191"
        ]);

        $user = auth("api")->user();
        $oldPhoto = $user->photo;
        if($request->image != $oldPhoto)
        {
            $name = time().'.'.explode("/",explode(":",substr($request->image,0,strpos($request->image,";")))[1])[1];

            \Image::make($request->image)->save(public_path('img/profile/').$name);

            $request->merge(["photo"=>$name]);

            //delete photo
            $userPhoto = public_path("img/profile/").$oldPhoto;
            if(file_exists($userPhoto) && $oldPhoto!= 'profile.png')
            {
                @unlink($userPhoto);
            }
        }
        $user->update($request->except(["password"]));
        
        return ["message"=>"success"];
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
        $user = User::findOrFail($id);

        $validateData = $request->validate([
            "name"=>"required|string|max:191",
            // "email"=>"required|email|max:191|unique:users,email",
            "password"=>"sometimes|confirmed|string|min:6",
            "type"=>"required|string|max:191"
        ]);

        $user->update($request->all());
        return ["message"=>"updated"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize("isAdmin");

        $user = User::findOrFail($id);
        $user->deleted= 1;
        $user->save();
        return "success";
    }

    public function search(Request $request)
    {
        // return $request->all();
        $search = $request->get("q");
        // return ["message"=>$search];
        if(!empty($search))
        {
            // $results = User::where("name","LIKE","%{$search}%")->orWhere("email","LIKE","%{$search}%")->orWhere("type","LIKE","%{$search}%")->get();

            $results = User::where(function($query) use ($search){
                $query->where("name","LIKE","%{$search}%")->orWhere("email","LIKE","%{$search}%")->orWhere("type","LIKE","%{$search}%");
            })->paginate(5);
            return $results;
        }
        
    }
}
