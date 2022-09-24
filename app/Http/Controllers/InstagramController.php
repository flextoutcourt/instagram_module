<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\File\File;

class InstagramController extends Controller
{
    protected string $baseUrlFB = 'https://facebook.com/v15.0';
    protected string $baseUrlGraph = 'https://graph.facebook.com/v15.0';

    protected string $redirect_uri;
    protected string $app_id = '';
    protected string $app_secret = '';
    protected string $scopes = 'instagram_basic,instagram_content_publish,pages_read_engagements,pages_show_list,public_profile';

    protected string $code = '';
    protected string $user_token;

    protected string $instagram_business_account;
    protected string $instagram_container_id;
    protected array $state;

    public function success(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('instagram.publish.success');
    }

    /**
     * Store files to storage
     *
     * @param $files
     * @return array
     */
    protected function store_files($files): array
    {
        $urls = [];
        foreach($files as $key => $file){
            if ($file->getClientOriginalExtension() === 'mp4'){
                // store video
                $file->storeAs('videos', $file->getClientOriginalName(), ['disk' => 'public']);
            }else{
                $file->storeAs('images', $file->getClientOriginalName(), ['disk' => 'public']);
            }
            $urls[] = URL::to('/') . '/storage/' . $file->getClientOriginalName();
        }
        return $urls;
    }

    public function store(PostRequest $request)
    {
        if(count($request->file('medias')) > 1) {
            $carousel = new InstagramCarouselController;
            $carousel->store($request);
        }

        if($request->file('medias')[0]->getClientOriginalExtension() === 'mp4') {
            $reels = new InstagramReelController;
            $reels->store($request);
        }

        $post = new InstagramPublishController;
        $post->store($request);
    }

    protected function set_redirect_uri()
    {
        $this->redirect_uri = Url::to('/',[], true) . '/instagram/publish/oauth_redirect';
    }

    protected function log_user(string $from, $request): void
    {
        $urls = implode(',', $this->store_files($request->file('medias')));
        $state = $from.'|'.json_encode($request->all()).'|'.$urls;
        $app_id = env('INSTAGRAM_APP_ID');
        $url = "{$this->baseUrlFB}/dialog/oauth?client_id={$app_id}&redirect_uri={$this->redirect_uri}&scopes={$this->scopes}&state={$state}";
        redirect()->away($url)->send();
    }

    private function store_token($token)
    {
        $this->user_token = $token;

        $this->get_page_id();
    }

    private function get_page_id()
    {
        $request = $this->curl_request($this->baseUrlGraph . '/me/accounts', 'GET', [
            'access_token' => $this->user_token
        ]);
        $r = json_decode($request);
        $this->page_id = $r->data[0]->id;
        $this->fetch_instagram_ba();
    }

    private function fetch_instagram_ba()
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $this->page_id . '/', 'GET', [
            'fields' => 'instagram_business_account',
            'access_token' => $this->user_token,
        ]);
        $r = json_decode($request);
        $this->instagram_business_account = $r->instagram_business_account->id;
        $this->redirect_to_controller();
    }

    protected function redirect_to_controller()
    {
        $controller = '';
        switch($this->state['from']){
            case 'post':
                $controller = new InstagramPublishController();
                break;
            case 'carousel':
                $controller = new InstagramCarouselController();
                break;
            case 'reel':
                $controller = new InstagramReelController();
                break;
            default:
                break;
        }
        $controller->create_container(['instagram_business_account' => $this->instagram_business_account, 'user_token' => $this->user_token, 'request' => $this->state]);
    }

    public function oauth_redirect(Request $request)
    {
        $this->set_redirect_uri();
        if ($request->has('code') && $request->has('state')) {
            $this->code = $request->get('code');
            $this->state = [
                'from' => explode('|', $request->get('state'))[0],
                'request' => json_decode(explode('|', $request->get('state'))[1]),
                'medias' => explode('|', $request->get('state'))[2],
            ];
            $this->get_access_token();
        }
    }

    private function get_access_token()
    {
        $request = $this->curl_request($this->baseUrlGraph . '/oauth/access_token', 'POST', [
            'client_id' => env('INSTAGRAM_APP_ID'),
            'client_secret' => env('INSTAGRAM_APP_SECRET'),
            'redirect_uri' => $this->redirect_uri,
            'code' => $this->code,
        ]);
        $r = json_decode($request);
        $this->store_token($r->access_token);
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $params params to be passed into request
     */
    protected function curl_request(string $endpoint, string $method = 'POST', array $params = []): bool|string
    {
        $url = $method === 'GET' ? $endpoint . '?' . http_build_query($params) : $endpoint;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $method === "POST" ? 1 : 0);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $r = json_decode($response);
        if (isset($r->error)) {
            dd($r->error);
        }
        return $response;
    }

    /**
     * @param array $params
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     */
    protected function publish_to_insta(array $params = []): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media_publish', 'POST', [
            'creation_id' => $params['instagram_container_id'],
            'access_token' => $params['user_token'],
        ]);
        $r = json_decode($request);
        return view('instagram.publish.success', ['r' => $r, 'flash' => ['type' => 'success', 'message' => 'Successfully published to Instagram']]);
    }
}
