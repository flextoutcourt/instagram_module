<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramCarouselController extends InstagramPublish
{

    protected array $params = [];

    public function index()
    {
        $this->set_redirect_uri();
        $this->log_user('carousel');
    }

    protected function create_container($params, $items = ['1', '2', '3', '4'])
    {
        $this->params = $params;
        $ids = [];
        foreach ($items as $i => $item) {
            $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media', 'POST', [
                "image_url" => "https://picsum.photos/id/{$item}/1920/1080",
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
            'caption' => 'Test intégration instagram Carousel from API'
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        $this->publish_to_insta(array_merge($this->params, ['instagram_container_id' => $r->id]));
    }
}
