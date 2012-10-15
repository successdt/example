<?php
App::import('vendor','facebook');
App::import('vendor','weibo');
App::import('vendor','meshtiles');
class SocialController extends AppController {
    var $name = 'Social';
    var $components = array(
        'OAuthConsumer',
        'Cookie');
    var $uses = array();
    var $weibo_config = array(
        'client_id' => '638765427',
        'client_secret' => 'e9e3c428e258707c9812a3d4338218c6',
        'grant_type' => 'authorization_code',
        'redirect_uri' => 'http://localhost.com/example/social/weibo_callback',
    );
    var $facebook_config = array(
        'appId' => '193983577403302',
        'secret' => '57ef4891b62cc96a0bcd1506db7190fd',
        'perms' => 'offline_access, user_groups, publish_stream,publish_actions',
        'cookie' => true
    );
    var $meshtiles_config = array(
        'app_key' => '333cd797b22b7393db2341008da143ae',
        'app_secret' => '2531eef83797e29026262670c952ed1b',
        'grant_type' => 'authorization_code',
        'redirect_uri' => 'http://localhost/instagram/meshtiles/success',
    );
    public function auth(){
        $request_token = $this->OAuthConsumer->getRequestToken('Twitter','https://api.twitter.com/oauth/request_token','http://localhost/example/social/twitter_reg');
        if ($request_token) {
            $this->Session->write('twitter_request_token', $request_token);
            $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $request_token->key);
        } else {
            // an error occured when obtaining a request token
        }
    }
    public function callback() {
        $requestToken = $this->Session->read('twitter_request_token');        
        $accessToken = $this->OAuthConsumer->getAccessToken('Twitter', 'https://api.twitter.com/oauth/access_token', $requestToken);
        if($accessToken)
            $this->Session->write('access_token', $accessToken);
        else {
           $accessToken = $this->Session->read('access_token'); 
           debug($accessToken);
        }
            
        
        $file = realpath('icon.png');
        debug($file);
        //$file = file_get_contents("default.jpeg"); 
        //$file = "http://zshop.vn/images/detailed/14/31278_iphone-4-slant13286726224f31ef6e78a1b.jpg";                       
        if ($accessToken) {
            /*            
            $result = $this->OAuthConsumer->post('Twitter', $accessToken->key, $accessToken->secret, 'https://api.twitter.com/1.1/statuses/update_with_media.json', array(
                'status' => 'hello world! '.rand(),
                'media[]'  => '@' . realpath('default.jpg')
            ));
            */
            $result = $this->OAuthConsumer->post('Twitter', $accessToken->key, $accessToken->secret, 'https://api.twitter.com/1.1/statuses/update.json', array(
                'status' => 'hello world! '.rand()
            ));                        
            debug($result);                
        }
    }
    public function twitter_reg(){
        $requestToken = $this->Session->read('twitter_request_token');        
        $accessToken = $this->OAuthConsumer->getAccessToken('Twitter', 'https://api.twitter.com/oauth/access_token', $requestToken);
        $response = $this->OAuthConsumer->get(
            'Twitter', 
            $accessToken->key, 
            $accessToken->secret, 
            'https://api.twitter.com/1.1/account/verify_credentials.json'
        );
        $result = json_decode($response,true);
        //debug($result);
        $mesh = new Meshtiles ($this->meshtiles_config);
        $cookie = $this->Cookie->read('MeshtilesAccessToken');
        debug($cookie);
        $mesh -> setAccessToken($cookie);
        $response = $mesh->registerLinktoTwitter($result['id'],$accessToken->key,$accessToken->secret,true);
        debug($response);
    }
    public function tumblr_auth(){
        $request_token = $this->OAuthConsumer->getRequestToken('Tumblr','http://www.tumblr.com/oauth/request_token','http://localhost.com/example/social/tumblr_callback');
        
        if ($request_token) {
            $this->Session->write('tumblr_request_token', $request_token);
            $this->redirect('http://www.tumblr.com/oauth/authorize?oauth_token=' . $request_token->key);
        } else {
            // an error occured when obtaining a request token
        }
        $this->render('auth');
    }
    public function tumblr_callback() {
        $requestToken = $this->Session->read('tumblr_request_token');
        $accessToken = $this->OAuthConsumer->getAccessToken('Tumblr', 'http://www.tumblr.com/oauth/access_token', $requestToken);
        if($accessToken)
            $this->Session->write('tumblr_access_token', $accessToken);
        else 
            $accessToken = $this->Session->read('tumblr_access_token');
        //debug($accessToken);
        //$file = file_get_contents("default.jpeg");
;
        $file = "https://twitter.com/images/default_profile_add_photo.png";
        debug($file);         
        if ($accessToken) {
            $result = $this->OAuthConsumer->post('Tumblr', $accessToken->key, $accessToken->secret, 'http://api.tumblr.com/v2/blog/thanhdd.tumblr.com/post', array(
                'type' =>'photo',
                'caption' => 'hello world! https://twitter.com/images/default_profile_add_photo.png '.rand(),
                'generator' => 'testing tumblr API example',
                'source'=>$file));
            debug($result);                
        }
        $this->render('callback');
    }
    public function weibo_auth(){
        $weibo = new Weibo($this->weibo_config);
        $weibo->openAuthorizationUrl();
        $this->render('auth');
    }
    public function weibo_callback() {
        $weibo = new Weibo($this->weibo_config);
        $access_token = $weibo->getAccessToken();
        $this->Session->write('weibo_token',$access_token);
        $this->render('callback');

    }
    function weibo_post(){
        $access_token = $this->Session->read('weibo_token');
        debug($access_token);
        $weibo = new Weibo($this->weibo_config);
        $weibo->setAccessToken($access_token);
        $status = "Hello weibo";
        $pic = realpath('icon.png');
        //$pic = 'https://twitter.com/images/default_profile_add_photo.png';
        $response = $weibo->postMediaStatus($status,$pic);
        debug(json_decode($response),true);
        $this->render('callback');
    }
    public function facebook_auth(){
        $facebook = new Facebook($this->facebook_config);
        //$this->set('login_url',$facebook->getLoginUrl(array('scope' => 'publish_stream')));
        $this->set('login_url',$facebook->getLoginUrl(array('scope' => array('publish_stream read_friendlists email'))));
    }
    public function facebook_upload(){
        $facebook = new Facebook($this->facebook_config);
        try{ 
            $comment = 'Test cái';
            $user = $facebook->getUser();
            debug($facebook->getAccessToken());
            $facebook->setFileUploadSupport(true);
            $args = array(       
                'message' => $comment);
            $args['image'] = '@' . realpath('default.jpg');
                            
            $response = $facebook->api('/me/photos','post',$args);                     
        }catch(FacebookApiException $e){
        	error_log($e);
            echo $e;
        }
        $this->render('callback');
    }
      
    public function location(){
        
    }
    public function facebook_location($lat = NULL , $lon = NULL){
        if($lat&&$lon){
            $facebook = new Facebook($this->facebook_config);
            try{ 
                debug($facebook->getAccessToken());
                $args = array(
                    'type' => 'place',
                    'center' => $lat.','.$lon,
                    'distance' => 1000
                );            
                $response = $facebook->api('/search','get',$args);
                debug($response);                     
            }catch(FacebookApiException $e){
            	error_log($e);
                echo $e;
            }
            
        }
        $this->render('callback');
    }
    public function facebook_email(){
        $client = new Facebook($this->facebook_config);
        
        // get all friends who has given our app permissions to access their data
        $user = $client->getUser();
        debug($user);
        /*
        $fql = "SELECT uid, first_name, last_name, email FROM user "
             . "WHERE is_app_user = 1 AND uid IN (SELECT uid2 FROM friend WHERE uid1 = $user)"; */
        $fql = "SELECT uid, first_name, last_name, email, name, username FROM user "
             . "WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = $user)";
        $friends = $client->api(array(
          'method'       => 'fql.query',
          'access_token' => $client->getAccessToken(),
          'query'        => $fql,
        ));
        //debug($friends);
        $cookie = $this->Cookie->read('MeshtilesAccessToken');
        debug($cookie);
        $mesh -> setAccessToken($cookie);
        $list = '';
        foreach ($friends as $friend){
            $list = $list.','.$friend['uid'];
        }
        $response = $mesh->searchFacebookFriends($list);
        $result = json_decode($response,true);
        debug($result);
          // don't use an access token

    }
    public function facebook_friend(){
        $facebook = new Facebook($this->facebook_config);
        $mesh = new Meshtiles ($this->meshtiles_config);
        $cookie = $this->Cookie->read('MeshtilesAccessToken');
        debug($cookie);
        $mesh -> setAccessToken($cookie);
        try{ 
            debug($facebook->getAccessToken());            
            $response = $facebook->api('/me/friends','get');
            $list = '';
            foreach ($response['data'] as $friend){
                $list = $list.','.$friend['id'];
            }
            $response = $mesh->searchFacebookFriends($list);
            $result = json_decode($response,true);
            debug($result);                     
        }catch(FacebookApiException $e){
        	error_log($e);
            echo $e;
        }
        $this->render('callback');
    }
    public function facebook_reg(){
        $facebook = new Facebook($this->facebook_config);
        $mesh = new Meshtiles ($this->meshtiles_config);
        $cookie = $this->Cookie->read('MeshtilesAccessToken');
        debug($cookie);
        $mesh -> setAccessToken($cookie);
        try{ 
            debug($facebook->getAccessToken());            
            $id = $facebook->getUser();
            debug($id);
            $response = $mesh->registerLinktoFacebook($id,$facebook->getAccessToken(),1);
            $result = json_decode($response,true);
            debug($result);                     
        }catch(FacebookApiException $e){
        	error_log($e);
            echo $e;
        }
        $this->render('callback');
    }
    public function twitter_friend(){
        
        $response = $this->OAuthConsumer->get(
            'Twitter','723755342-hn6IzFs6KoieDA1Ks05KNBHZViaFoKSncKoZdbQ',
            'UROerEW3exDtqstfmqxOd0lHvGLXsuaj6yqmgvUcpk',
            'https://api.twitter.com/1.1/friends/ids.json');
        $result = json_decode($response,true);
        $list = '';
        foreach($result['ids'] as $friend){
            $list = $list.','.$friend;
        }
        $mesh = new Meshtiles ($this->meshtiles_config);
        $cookie = $this->Cookie->read('MeshtilesAccessToken');
        debug($cookie);
        $mesh -> setAccessToken($cookie);
        $response = $mesh->searchTwitterFriends($list);
        $result = json_decode($response,true);
        debug($result);
        $this->render('callback'); 
    }    
}
 ?>