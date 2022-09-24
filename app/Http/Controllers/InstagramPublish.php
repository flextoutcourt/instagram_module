<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class InstagramPublish extends InstagramController
{

    public function index()
    {
        $this->set_redirect_uri();
        $this->log_user('post');
    }

    protected function create_container($params)
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
            "image_url" => "https://picsum.photos/1920/1080",
            'caption' => 'Test intégration instagram from API',
            'access_token' => $params['user_token'],
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        $this->publish_to_insta(array_merge($params, ['instagram_container_id' => $r->id]));
    }

}
