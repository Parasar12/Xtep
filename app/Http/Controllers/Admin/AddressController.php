<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\District;

class AddressController extends Controller
{

    public function index()
    {
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        return view('frontend.address.index', compact('addresses'));
    }

    public function getProviceDistrict($id)
    {
        $districts = District::where('zone_state_id', $id)->get();
        return view('frontend.address.province_disticts', compact('districts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street' => 'required|max:255',
        ]);
        $address = new Address();
        $address->user_id = auth()->user()->id;
        $address->province_id = $request->province_id;
        $address->district_id = $request->district_id;
        $address->street = $request->street;
        $address->save();

        return redirect()->back()->with(['success' => "User Address Created Successfully.", 'reload' => true]);;
    }
}
