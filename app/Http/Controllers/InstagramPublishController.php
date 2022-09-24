<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;

class InstagramPublishController extends InstagramController
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('instagram.publish.post');
    }

    public function store(PostRequest $request)
    {
//        dd($request->file('medias'));
        if(count($request->file('medias')) > 1) {
            $carousel = new InstagramCarouselController;
            $carousel->store($request);
        }
        $this->set_redirect_uri();
        $this->log_user('post', $request);
    }

    protected function create_container($params)
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
            "image_url" => $params['request']['medias'],
            'caption' => $params['request']['request']->caption,
            'access_token' => $params['user_token'],
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        $this->publish_to_insta(array_merge($params, ['instagram_container_id' => $r->id]));
    }

}
