<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarouselRequest;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class InstagramCarouselController extends InstagramPublishController
{

    protected array $params = [];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('instagram.publish.carousel');
    }

    /**
     * @param \App\Http\Requests\PostRequest $request
     * @return void
     */
    public function store(PostRequest $request): void
    {
        $this->set_redirect_uri();
        $this->log_user('carousel', $request);
    }

    protected function create_container($params)
    {
        $items = explode(',', $params['request']['medias']);
        $this->params = $params;
        $ids = [];
        foreach ($items as $i => $item) {
            $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
                "image_url" => $item,
                'is_carousel_item' => true,
                'access_token' => $params['user_token'],
            ]);
            $r = json_decode($request);
            $ids[] = $r->id;
        }
        $this->create_carousel_container($ids);
    }

    /**
     * @param array $ids
     * @return void
     */
    private function create_carousel_container(array $ids = []): void
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $this->params['instagram_business_account'] . '/media', 'POST', [
            'access_token' => $this->params['user_token'],
            'children' => implode(',', $ids),
            'media_type' => 'CAROUSEL',
            'caption' => $this->params['request']['request']->caption,
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        $this->publish_to_insta(array_merge($this->params, ['instagram_container_id' => $r->id]));
    }
}
