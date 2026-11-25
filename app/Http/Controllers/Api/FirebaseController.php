<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FcmToken;

use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;

use App\Models\User;
use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client;

class FirebaseController extends Controller{

    public function sendNotification(Request $request){
        $rules = array(
            'user_id' => 'required|numeric',
            'title' => 'required',
            'description' => 'required',
        );

        $messages = array(
            'user_id.required' => 'user_id is required.',
            'title.required' => 'title is required.',
            'description.required' => 'description is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        }
        else{
            $userId = $request->user_id;
            $title = $request->title;
            $description = $request->description;

            $imageName = null;

            if ($request->file('image')) {
                $extension = $request->image->extension();
                $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('/uploads/notifications'), $imageName);
            }

            $user = User::where('id', $userId)->first();
            if ($user) {
                if ($user->fcm_token && $user->notification_status == 1) {
                    $fcmNotification = [
                        'title' => $title,
                        'body' > $description,
                        'icon' => 'img/icon.png',
                        'image' => url('uploads/notifications/' . $imageName)
                    ];

                    $payload = [
                        'registration_ids' => [$user->fcm_token],
                        'data' => $fcmNotification
                    ];

                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $client = new Client();
                    $req = $client->post(
                        $url,
                        array(
                            'headers' => [
                                'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
                                'Content-Type' => 'application/json',
                            ],
                            'body' => json_encode($payload),
                            'timeout' => 5
                        )
                    );

                    $response = json_decode($req->getBody(), true);

                    return $response;
                } 
                else {
                    return ApiResponse::sendNoDataErrorResponse('Notification not enabled.');
                }
            }

            return ApiResponse::sendNoDataErrorResponse('User not found.');
        }
    }

    public function saveUserFcmToken(Request $request){
        $rules = array(
            'fcm_token' => 'required',
        );

        $messages = array(
            'fcm_token.required' => 'fcm_token is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        }
        else{

            DB::table('fcm_token')->where('user_id', auth()->user()->id)->delete();

            $fcm = new FcmToken();
            $fcm->user_id = auth()->user()->id;
            $fcm->fcm_token = $request->fcm_token;
            $fcm->created_date = date('Y-m-d H:i:s');
            $fcm->save();

            return ApiResponse::sendSuccessResponse('FCm Token Saved Successfully');
        }
    }
}
