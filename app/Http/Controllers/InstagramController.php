<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    protected string $from;

    protected function set_redirect_uri()
    {
        $this->redirect_uri = 'https://quentinleclerc.fr/instagram/publish/oauth_redirect';
    }

    protected function log_user(string $from): void
    {
        $app_id = env('INSTAGRAM_APP_ID');
        $url = "{$this->baseUrlFB}/dialog/oauth?client_id={$app_id}&redirect_uri={$this->redirect_uri}&scopes={$this->scopes}&state={$from}";
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
        switch($this->from){
            case 'post':
                $controller = new InstagramPublish();
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
        $controller->create_container(['instagram_business_account' => $this->instagram_business_account, 'user_token' => $this->user_token]);
    }

    public function oauth_redirect(Request $request)
    {
        $this->set_redirect_uri();
        if ($request->has('code')) {
            $this->code = $request->get('code');
            $this->from = $request->get('state');
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
    protected function curl_request(string $endpoint, string $method = 'POST', array $params = [])
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
     * @return void
     */
    protected function publish_to_insta(array $params = [])
    {
        $request = $this->curl_request($this->baseUrlGraph . '/' . $params['instagram_business_account'] . '/media_publish', 'POST', [
            'creation_id' => $params['instagram_container_id'],
            'access_token' => $params['user_token'],
        ]);
        $r = json_decode($request);
    }
}
