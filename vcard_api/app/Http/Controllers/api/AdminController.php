<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return AdminResource::collection(Admin::all());
    }

    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    public function store(StoreAdminRequest $request)
    {
        $formData = $request->validated();
        $formData["password"] = bcrypt($formData["password"]);
        $newAdmin = Admin::create($formData);
        return new AdminResource($newAdmin);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin->update($request->validated());
        return new AdminResource($admin);
    }

    public function update_password(UpdatePasswordRequest $request, Admin $admin)
    {
        $formData = $request->validated();
        if (!Hash::check($formData['current_password'], $admin->password))
            abort(422);
        $admin->password = bcrypt($request->validated()['password']);
        $admin->save();
        return new AdminResource($admin);
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return new AdminResource($admin);
    }

    public function totalNumOfAdmins(Request $request)
    {
        $totalNumOfAdmins = Admin::count();
        return $totalNumOfAdmins;
    }
}
