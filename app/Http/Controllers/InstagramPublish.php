<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class InstagramPublish extends Controller
{

    private string $baseUrlFB = 'https://facebook.com/v15.0';
    private string $baseUrlGraph = 'https://graph.facebook.com/v15.0';

    private string $redirect_uri;
    private string $app_id = '';
    private string $app_secret = '';
    private string $scopes = 'instagram_basic,instagram_content_publish,pages_read_engagements,pages_show_list,public_profile';

    private string $code = '';
    private string $user_token;

    private string $instagram_business_account;
    private string $instagram_container_id;

    private function set_redirect_uri()
    {
        $this->redirect_uri = URL::to('/', [], true).'/instagram/publish/oauth_redirect';
        dd($this->redirect_uri);
    }

    public function check_user()
    {
        $this->set_redirect_uri();
        $this->log_user();
    }

    private function log_user(): void
    {
        $app_id = env('INSTAGRAM_APP_ID');
        $url = "{$this->baseUrlFB}/dialog/oauth?client_id={$app_id}&redirect_uri={$this->redirect_uri}&scopes={$this->scopes}";
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
        $this->create_container();
    }

    private function create_container()
    {
        $request = $this->curl_request($this->baseUrlGraph.'/'.$this->instagram_business_account.'/media', 'POST', [
            'image_url' => 'https://picsum.photos/1920/1080',
            'caption' => "Test d'implémentation Instagram",
            'access_token' => $this->user_token,
        ]);
        $r = json_decode($request);
        $this->instagram_container_id = $r->id;
        $this->publish_to_insta();
    }

    private function publish_to_insta()
    {
        $request = $this->curl_request($this->baseUrlGraph.'/'.$this->instagram_business_account.'/media_publish', 'POST', [
            'creation_id' => $this->instagram_container_id,
            'access_token' => $this->user_token,
        ]);
        $r = json_decode($request);
        dd($r);
    }

    public function oauth_redirect(Request $request)
    {
        $this->set_redirect_uri();
        if ($request->has('code')) {
            $this->code = $request->get('code');
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
    private function curl_request(string $endpoint, string $method = 'POST', array $params = [])
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

    public function index()
    {
        return view('post.post');
    }
}
