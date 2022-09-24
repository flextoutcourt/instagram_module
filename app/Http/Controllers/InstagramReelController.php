<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;

class InstagramReelController extends InstagramController
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('instagram.publish.reels');
    }

    public function store(PostRequest $request)
    {
        $this->set_redirect_uri();
        $this->log_user('reel', $request);
    }

    protected function create_container($params)
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
            "video_url" => $params['request']['medias'],
            'caption' => $params['request']['request']->caption,
            'access_token' => $params['user_token'],
            'media_type' => 'REELS'
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        sleep(20);
        $this->publish_to_insta(array_merge($params, ['instagram_container_id' => $r->id]));
    }
}
