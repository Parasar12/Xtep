<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\SocialSite;
use Illuminate\Support\Facades\Validator;

class SocialSiteController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('MasterSetup-SocialSite');

        return view('admin.social_site.index');
    }

    public function getSocialSites(Request $request)
    {
        $this->_hasMenuAccess('MasterSetup-SocialSite');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];


        /*Total records*/
        $totalRecords = SocialSite::select('count(*) as allcount')->count();
        $totalRecordswithFilter = SocialSite::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = SocialSite::orderBy($columnName, $columnSortOrder)
                ->where('social_sites.name', 'like', '%' . $searchValue . '%')
                ->select('id', 'name', 'url', 'is_active')
                ->take($rowperpage)
                ->get();
        } else {
            $records = SocialSite::orderBy($columnName, $columnSortOrder)
                ->where('social_sites.name', 'like', '%' . $searchValue . '%')
                ->select('id', 'name', 'url', 'is_active')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            $url = $record->url;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "url" => $url,
                "is_active" => $status,
                'action' => "<a class='edit_social_sites btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('social_site.edit', $id) . "'>Edit</a>"
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        print(json_encode($response));
        exit();
    }

    public function store(Request $request)
    {
        $this->_hasMenuAccess('MasterSetup-SocialSite');

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'url' => 'required|max:255|url',
            'icon_class' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $site = new SocialSite();
            $site->name = $request->name;
            $site->url = $request->url;
            $site->is_active = true;
            $site->icon_class = $request->icon_class;

            $site->save();

            return response()->json(['status' => 1, 'msg' => "Social Site Added Successfully."]);
        }


    }

    public function edit($id)
    {
        $this->_hasMenuAccess('MasterSetup-SocialSite');

        $socialSite = SocialSite::where('id', $id)->first();
        return view('admin.social_site.edit', compact('socialSite'));

    }

    public function update(Request $request)
    {
        $this->_hasMenuAccess('MasterSetup-SocialSite');

        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'url' => 'required|max:255',
            'icon_class' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $site = SocialSite::find($id);

            if ($request->is_active == 'on') {
                $site->is_active = 1;
            } else {
                $site->is_active = 0;
            }

            $site->name = $request->name;
            $site->url = $request->url;
            $site->icon_class = $request->icon_class;

            $site->save();

            return response()->json(['status' => 1, 'msg' => "Social Site Updated Successfully."]);
        }
    }

}
