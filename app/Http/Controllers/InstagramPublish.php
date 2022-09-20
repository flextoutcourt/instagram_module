<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramPublish extends Controller
{

    private $redirect_uri;
    private $app_id;
    private $app_secret;
    private $scopes = 'instagram_basic,instagram_content_publish,pages_read_engagements,pages_show_list,public_profile';

    public function check_user()
    {
        try{

        }catch(\Exception $e){
            // redirect with error

        }
    }

    private function log_user()
    {

    }

    private function store_token()
    {

    }

    private function get_page_id()
    {

    }

    private function create_container()
    {

    }

    private function publish_to_insta()
    {

    }

    public function oauth_redirect()
    {

    }
}
