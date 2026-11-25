<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUs::first();
        return view('admin.about.index', compact('about'));
    }

    public function update(Request $request)
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                $about = AboutUs::first();

                if ($request->file('image')) {
                    $imageName = 'about/' . date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                    $request->file('image')->move(public_path('/uploads/about'), $imageName);
                    $about->featured_image = $imageName;
                }

                $about->title = $request->title;
                $about->description = $request->description;
                $about->save();

                $output = array(
                    'response' => true,
                    'msg' => "Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }
}
