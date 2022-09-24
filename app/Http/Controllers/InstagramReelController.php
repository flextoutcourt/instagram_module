<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramReelController extends InstagramController
{

    public function index()
    {
        $this->set_redirect_uri();
        $this->log_user('reel');
    }

    protected function create_container($params)
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
            "video_url" => "https://quentinleclerc.fr/video.mp4",
            'caption' => 'Test intégration instagram from API',
            'access_token' => $params['user_token'],
            'media_type' => 'REELS'
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        sleep(20);
        $this->publish_to_insta(array_merge($params, ['instagram_container_id' => $r->id]));
    }
}
