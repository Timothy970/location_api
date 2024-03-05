<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'data'=>$users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);

            $validatedData['password'] = bcrypt($validatedData['password']);
            User::create($validatedData);

            $token = $user->generateToken();
            return response()->json([
                'user_id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'token'=>$token  
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => "Unable to create user: {$e->getMessage()}"
            ]);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,

            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => "Unable to create user: {$e->getMessage()}"
            ], 500);
        }

    }
    public function login(Request $request){
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            try{
                $user = User::where('email', $request->email)->first();
    
                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'Invalid credentials'], 401);
            }      
                $token = $user->generateToken();
                return response()->json([
                'user_id' => $user->id,
                'name' => $user->name,
                'token' => $token,
                
                ]);
            }catch(Exception $e){
                return response()->json([
                    'message' => "Unable to login, enter correct credentials: {$e->getMessage()}"
                ], 500);
            }
    }
} 
