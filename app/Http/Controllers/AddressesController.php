<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Address;
use App\User;
use Illuminate\Validation\Validator;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressCollection;

class AddressesController extends Controller
{
    use SearchableTrait, SoftDeletes;
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string',
            'state' => 'required|string',
            'local_government' => 'required|string',
            'region' => 'required|string',
            'postal_code' => 'required|numeric|max:255',
            'longitude' => 'numeric|max:255|nullable',
            'latitude' => 'numeric|max:255|nullable',
            'nearest_landmark' => 'string|nullable',
            'street_address' => 'string|required',
        ]);

        if ($validator->fails) {
            return response()->json($validator->messages, 422);
        }

        $data = collect($request->all())->toArray();
        $data['user_id'] = Auth::user()->id;
        $result = Address::create($data);

        if (!$request->longitude && !$request->latitude) {
            return $this->geo_location_address;
        }

        if ($result) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json([
                'data' => false, 'error' => 'an unknown error occured'
            ], 404);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string',
            'state' => 'required|string',
            'local_government' => 'required|string',
            'region' => 'required|string',
            'postal_code' => 'required|numeric|max:255',
            'longitude' => 'numeric|max:255|nullable',
            'latitude' => 'numeric|max:255|nullable',
            'nearest_landmark' => 'string|nullable',
            'street_address' => 'string|required',
        ]);

        if ($validator->fails) {
            return response()->json($validator->messages, 422);
        }

        $data = collect($request->all())->toArray();
        $data['user_id'] = Auth::user()->id;
        $result = Address::update($data);

        if (!$request->longitude && !$request->latitude) {
            return $this->geo_location_address;
        }

        if ($result) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json([
                'data' => false, 'error' => 'an unknown error occured'
            ], 404);
        }
    }

    public function show(Request $request)
    {
        $id = (int)$request->route('id');
        if ($id) {
            $data =Auth::user()->address()->find($id);
            return response()->json([
                'data' => $data
            ], 200);
        } else {
            return response()->json(false, 404);
        }
    }

    // public function list(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'q' => 'nullable|min:3'
    //     ]);

    //     $query = ('q');
    //     $data = collect($request->all())->toArray();
    //     $addresses = Address::where('Address', '>', '0');

    //     if ($query) {
    //         $address = $addresses->search($query);
    //     }

    //     $length = (int)(empty($request['per_page']) ? 15 : $request['per_page']);
    //     $address = $addresses->paginate($length);
    //     $data = new AddressCollection($address);

    //     return response()->json($data);
    // }

    public function delete(Request $request)
    {
        $id = (int)$request->route['id'];

        if ($id) {
           $address =Auth::user()->address()->find($id);
           $address->delete();
            return response()->json([
                'data' => true
            ], 204);
        } else {
            return response()->json(false, 500);
        }
    }

    public function restore(Request $request)
    {
        $id = (int)$request->route['id'];

        if ($id) {
            Address::onlyTrashed()->find($id)->restore();
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 404);
        }
    }
}
