<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $customers = Customer::with('sales', 'user')->withCount('sales')->paginate($perPage);

        return response()->json($customers);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        if ($user?->role != 'admin' || $user?->id != $id) {
            return response()->json(['errors' => [
                'message' => 'action unauthorized.'
            ]], 403);
        }

        $customer = Customer::with(['sales' => 'purchases'], 'user')->withCount(['sales' => 'purchases_count'])->find($id);

        if (!$customer) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'address_line_1' => 'required',
            'address_line_2' => 'nullable|string',
            'city' => 'required',
            'parish' => 'required',
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $user = auth()->user();

        if ($user?->role != 'admin' || $user?->id != $id) {
            return response()->json(['errors' => [
                'message' => 'action unauthorized.'
            ]], 403);
        }

        $customer = Customer::with(['sales' => 'purchases'], 'user')->withCount(['sales' => 'purchases_count'])->find($id);

        if (!$customer) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        $user = User::find($customer?->user_id);
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ]);

        $customer->update([
            'phone_number' => $request->input('phone_number'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'parish' => $request->input('parish'),
        ]);

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        $user = User::find($customer?->user_id);

        // will cascade to customer
        $user->delete();

        return response()->json([
            'message' => 'customer deleted successfully',
        ]);
    }
}
