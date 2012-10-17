<?php
/**
 * Instagram PHP implementation API
 * URLs: http://www.mauriciocuenca.com/
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
// require_once 'Zend/Http/Client.php';
require_once 'CurlHttpClient.php';

class Meshtiles {

    /**
     * The name of the GET param that holds the authentication code
     * @var string
     */
    const RESPONSE_CODE_PARAM = 'code';

    /**
     * Format for endpoint URL requests
     * @var string
     */
    protected $_endpointUrls = array(
        'authorize'=>'http://107.20.246.0/oauth/authorize?app_key=%s&app_secret=%s&callbackURL=%s',
        'access_token' => 'https://api.instagram.com/oauth/access_token',
        'user' => 'http://107.20.246.0/api/User/getUserProfile?app_key=%s&app_secret=%s&access_token=%s',
        'user_view' => 'http://107.20.246.0/api/View/getUserViewDetail?app_key=%s&app_secret=%s&access_token=%s&viewed_user_id=%s',
        'user_data'=>'http://107.20.246.0/api/User/getYOUData?app_key=%s&app_secret=%s&access_token=%s',
        'user_status'=>'http://107.20.246.0/api/User/getUserStatus?app_key=%s&app_secret=%s&access_token=%s'
,       'user_feed' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&max_id=%s&min_id=%s',
        'user_feed2' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&max_tag_id=%s',
        'user_recent' => 'http://107.20.246.0/api/View/getPhotoOfUser?app_key=%s&app_secret=%s&access_token=%s&viewed_id=%s&page_index=%d',
        'user_search' => 'http://107.20.246.0/api/SignUp/getListUserByUserName?app_key=%s&app_secret=%s&access_token=%s&keyword=%s',
        'user_follows'=>'http://107.20.246.0/api/View/getListUserFollow?app_key=%s&app_secret=%s&access_token=%s&viewed_user_id=%s&is_following=%s&page_index=%d',
        'user_followed_by' => 'https://api.instagram.com/v1/users/%d/followed-by?access_token=%s&cursor=%s',
        'user_requested_by' => 'https://api.instagram.com/v1/users/self/requested-by?access_token=%s',
        'user_relationship' => 'https://api.instagram.com/v1/users/%s/relationship?access_token=%s',
        'modify_user_relationship' => 'https://api.instagram.com/v1/users/%s/relationship?action=%s&access_token=%s',
        'media' => 'http://107.20.246.0/api/View/getPhotoDetail?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'media_search' => 'https://api.instagram.com/v1/media/search?lat=%s&lng=%s&max_timestamp=%d&min_timestamp=%d&distance=%d&access_token=%s',
        'media_popular' => 'http://107.20.246.0/api/View/getListPopularPhoto?app_key=%s&app_secret=%s&access_token=%s&page_index=%d',
        'media_comments' => 'http://107.20.246.0/api/View/getCommentOfPhoto?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',        
        'post_media_comment' => 'http://107.20.246.0/api/View/postComment',
        'delete_media_comment' => 'https://api.instagram.com/v1/media/%d/comments?comment_id=%d&access_token=%s',
        'likes' => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'post_like' => 'http://107.20.246.0/api/View/buttonClick?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'registerLinkToOtherServices' => 'http://107.20.246.0/api/SignUp/registerLinkToOtherServices',
        'remove_like' => 'http://107.20.246.0/api/View/cancelLikePhoto?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'searchFriendFromOtherServices' => 'http://107.20.246.0/api/SignUp/searchFriendFromOtherServices',
        'tags' => 'https://api.instagram.com/v1/tags/%s?access_token=%s',
        'tags_recent' => 'http://107.20.246.0/api/View/getListPhotoByTags?app_key=%s&app_secret=%s&access_token=%s&tag=%s&page_index=%d',
        'tags_recent2' => 'https://api.instagram.com/v1/tags/%s/media/recent?max_tag_id=%s&access_token=%s',
        'tags_search' => 'http://107.20.246.0/api/Photo/getListTagsRecommend?app_key=%s&app_secret=%s&access_token=%s&keyword=%s',
        'favourist_tag'=>'http://107.20.246.0/api/Photo/getFavouristTags?app_key=%s&app_secret=%s&access_token=%s',
        'locations' => 'https://api.instagram.com/v1/locations/%d?access_token=%s',
        'locations_recent' => 'http://107.20.246.0/api/View/getListPhotoByLocationID?app_key=%s&app_secret=%s&access_token=%s&location_id=%s&page_index=%d',
        'locations_recent2' => 'https://api.instagram.com/v1/locations/%s/media/recent/?max_id=%s&access_token=%s',
        'locations_search' => 'https://api.instagram.com/v1/locations/search?lat=%s&lng=%s&foursquare_id=%d&distance=%d&access_token=%s',
    );

    /**
    * Configuration parameter
    */
    protected $_config = array();

    /**
     * Whether all response are sent as JSON or decoded
     */
    protected $_arrayResponses = false;

    /**
     * OAuth token
     * @var string
     */
    protected $_oauthToken = null;

    /**
     * OAuth token
     * @var string
     */
    protected $_accessToken = null;
    
    /**
     * OAuth user object
     * @var object
     */
    protected $_currentUser = null;

    /**
     * Holds the HTTP client instance
     * @param Zend_Http_Client $httpClient
     */
    protected $_httpClient = null;

    /**
     * Constructor needs to receive the config as an array
     * @param mixed $config
     */
    public function __construct($config = null, $arrayResponses = false) {
        $this->_config = $config;
        $this->_arrayResponses = $arrayResponses;
        if (empty($config)) {
            throw new InstagramException('Configuration params are empty or not an array.');
        }
    }

    /**
     * Instantiates the internal HTTP client
     * @param string $uri
     * @param string $method
     */
    protected function _initHttpClient($uri, $method = CurlHttpClient::GET) {
        if ($this->_httpClient == null) {
            $this->_httpClient = new CurlHttpClient($uri);
        } else {
            $this->_httpClient->setUri($uri);
        }
        $this->_httpClient->setMethod($method);
    }

    /**
     * Returns the body of the HTTP client response
     * @return string
     */
    protected function _getHttpClientResponse() {
        return $this->_httpClient->getResponse();
    }

    /**
     * Retrieves the authorization code to be used in every request
     * @return string. The JSON encoded OAuth token
     */
    protected function _setOauthToken() {
        $this->_initHttpClient($this->_endpointUrls['access_token'], CurlHttpClient::POST);
        $this->_httpClient->setPostParam('client_id', $this->_config['client_id']);
        $this->_httpClient->setPostParam('client_secret', $this->_config['client_secret']);
        $this->_httpClient->setPostParam('grant_type', $this->_config['grant_type']);
        $this->_httpClient->setPostParam('redirect_uri', $this->_config['redirect_uri']);
        $this->_httpClient->setPostParam('code', $this->getAccessCode());

        $this->_oauthToken = $this->_getHttpClientResponse();
    }

    /**
     * Return the decoded plain access token value
     * from the OAuth JSON encoded token.
     * @return string
     */
    public function getAccessToken() {
        if ($this->_accessToken == null) {
          
            if ($this->_oauthToken == null) {
                $this->_setOauthToken();
            }
          
            $this->_accessToken = json_decode($this->_oauthToken)->access_token;
        }

        return $this->_accessToken;
    }
    
    /**
     * Return the decoded user object
     * from the OAuth JSON encoded token
     * @return object
     */
    public function getCurrentUser() {
        if ($this->_currentUser == null) {
            
            if ($this->_oauthToken == null) {
                $this->_setOauthToken();
            }
            
            $this->_currentUser = json_decode($this->_oauthToken)->user;
        }

        return $this->_currentUser;
    }

    /**
     * Gets the code param received during the authorization step
     */
    protected function getAccessCode() {
        //return $_GET[self::RESPONSE_CODE_PARAM];
    }

    /**
     * Sets the access token response from OAuth
     * @param string $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->_accessToken = $accessToken;
    }

    /**
     * Surf to Instagram credentials verification page.
     * If the user is already authenticated, redirects to
     * the URI set in the redirect_uri config param.
     * @return string
     */
    public function openAuthorizationUrl() {
        $authorizationUrl = sprintf($this->_endpointUrls['authorize'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->_config['redirect_uri'],
            self::RESPONSE_CODE_PARAM);
        //var_dump($authorizationUrl);
        header('Location: ' . $authorizationUrl);
        exit(1);
    }

    /**
      * Get basic information about a user.
      * @param $id
      */
    public function getUser() {
        $endpointUrl = sprintf($this->_endpointUrls['user'], 
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserViewDetail($viewed_user_id) {
        $endpointUrl = sprintf($this->_endpointUrls['user_view'], 
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $viewed_user_id);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserData(){
        $endpointUrl = sprintf($this->_endpointUrls['user_data'], 
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    /**
     * See the authenticated user's feed.
     * @param integer $maxId. Return media after this maxId.
     * @param integer $minId. Return media before this minId.
     */
    public function getUserStatus(){
        $endpointUrl = sprintf($this->_endpointUrls['user_status'], 
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();        
    }
    public function getUserFeed($maxId = null, $minId = null) {
        $endpointUrl = sprintf($this->_endpointUrls['user_feed2'], $this->getAccessToken(), $maxId, $minId);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get the most recent media published by a user.
     * @param $id. User id
     * @param $maxId. Return media after this maxId
     * @param $minId. Return media before this minId
     * @param $maxTimestamp. Return media before this UNIX timestamp
     * @param $minTimestamp. Return media after this UNIX timestamp
     */
    public function getUserRecent($id, $page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['user_recent'],            
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $id,
            $page_index);
        
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for a user by name.
     * @param string $name. A query string
     */
    public function searchUser($keyword) {
        $endpointUrl = sprintf($this->_endpointUrls['user_search'],
            $this->_config['app_key'],
            $this->_config['app_secret'], 
            $this->getAccessToken(),
            $keyword);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get the list of users this user follows.
     * @param integer $id. The user id
     */
    public function getUserFollows($id,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['user_follows'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $id,
            'true',
            $page_index);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get the list of users this user is followed by.
     * @param integer $id
     */
    public function getUserFollowedBy($id,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['user_follows'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $id,
            'false',
            $page_index);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * List the users who have requested this user's permission to follow
     */
    public function getUserRequestedBy() {
        $endpointUrl = sprintf($this->_endpointUrls['user_requested_by'], $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get information about the current user's relationship (follow/following/etc) to another user.
     * @param integer $id
     */
    public function getUserRelationship($id) {
        $endpointUrl = sprintf($this->_endpointUrls['user_relationship'], $id, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Modify the relationship between the current user and the target user
     * In order to perform this action the scope must be set to 'relationships'
     * @param integer $id
     * @param string $action. One of follow/unfollow/block/unblock/approve/deny
     */
    public function modifyUserRelationship($id, $action) {
        $endpointUrl = sprintf($this->_endpointUrls['modify_user_relationship'], $id, $action, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl, Zend_Http_Client::POST);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get information about a media object.
     * @param integer $mediaId
     */
    public function getMedia($id) {
        
        $endpointUrl = sprintf($this->_endpointUrls['media'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for media in a given area.
     * @param float $lat
     * @param float $lng
     * @param integer $maxTimestamp
     * @param integer $minTimestamp
     * @param integer $distance
     */
    public function mediaSearch($lat, $lng, $maxTimestamp = '', $minTimestamp = '', $distance = '') {
        $endpointUrl = sprintf($this->_endpointUrls['media_search'], $lat, $lng, $maxTimestamp, $minTimestamp, $distance, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a list of what media is most popular at the moment.
     */
    public function getPopularMedia($page_index=1) {
        $endpointUrl = sprintf($this->_endpointUrls['media_popular'],            
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $page_index );
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a full list of comments on a media.
     * @param integer $id
     */
    public function getMediaComments($id) {
        $endpointUrl = sprintf($this->_endpointUrls['media_comments'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    /**
     * Create a comment on a media.
     * @param integer $id
     * @param string $text
     */
    public function postMediaComment($id, $text) {
        $endpointUrl = $this->_endpointUrls['post_media_comment'] ;
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('comment',$text);
        $this->_httpClient->setPostParam('photo_id',$id);
        return $this->_getHttpClientResponse();
    }

    /**
     * Remove a comment either on the authenticated user's media or authored by the authenticated user.
     * @param integer $mediaId
     * @param integer $commentId
     */
    public function deleteComment($mediaId, $commentId) {
        $endpointUrl = sprintf($this->_endpointUrls['delete_media_comment'], $mediaId, $commentId, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl, CurlHttpClient::DELETE);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a list of users who have liked this media.
     * @param integer $mediaId
     */
    public function getLikes($mediaId) {
        $endpointUrl = sprintf($this->_endpointUrls['likes'], $mediaId, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Set a like on this media by the currently authenticated user.
     * @param integer $mediaId
     */
    public function postLike($mediaId) {
        $endpointUrl = sprintf($this->_endpointUrls['post_like'], 
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $mediaId);
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('access_token', $this->getAccessToken());
        return $this->_getHttpClientResponse();
    }

    /**
     * Remove a like on this media by the currently authenticated user.
     * @param integer $mediaId
     */
    public function removeLike($mediaId) {
        $endpointUrl = sprintf($this->_endpointUrls['remove_like'],             
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $mediaId);
        $this->_initHttpClient($endpointUrl, CurlHttpClient::DELETE);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get information about a tag object.
     * @param string $tagName
     */
    public function getTags($tagName) {
        $endpointUrl = sprintf($this->_endpointUrls['tags'], $tagName, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a list of recently tagged media.
     * @param string $tagName
     * @param integer $maxId
     * @param integer $minId
     */
    public function getRecentTags($tag,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['tags_recent'],
            $this->_config['app_key'],
            $this->_config['app_secret'], 
            $this->getAccessToken(),
            $tag,
            $page_index);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for tags by name - results are ordered first as an exact match, then by popularity.
     * @param string $tagName
     */
    public function searchTags($keyword) {
        $endpointUrl = sprintf($this->_endpointUrls['tags_search'],
            $this->_config['app_key'],
            $this->_config['app_secret'], 
            $this->getAccessToken(),
            $keyword);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getFavouristTags(){
        $endpointUrl = sprintf($this->_endpointUrls['favourist_tag'],
            $this->_config['app_key'],
            $this->_config['app_secret'], 
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
                
    }

    /**
     * Get information about a location.
     * @param integer $id
     */
    public function getLocation($id) {
        $endpointUrl = sprintf($this->_endpointUrls['locations'], $id, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a list of recent media objects from a given location.
     * @param integer $locationId
     */
    public function getLocationRecentMedia($location_id = '', $page_idex = '') {
        $endpointUrl = sprintf($this->_endpointUrls['locations_recent'],
            $this->_config['app_key'],
            $this->_config['app_secret'], 
            $this->getAccessToken(),
            $location_id,
            $page_idex
        );
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for a location by name and geographic coordinate.
     * @see http://instagr.am/developer/endpoints/locations/#get_locations_search
     * @param float $lat
     * @param float $lng
     * @param integer $foursquareId
     * @param integer $distance
     */
    public function searchLocation($lat, $lng, $foursquareId = '', $distance = '') {
        $endpointUrl = sprintf($this->_endpointUrls['locations_search'], $lat, $lng, $foursquareId, $distance, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        //var_dump($endpointUrl);        
        return $this->_getHttpClientResponse();
    }
    public function searchFacebookFriends($list_id){
        $endpointUrl = $this->_endpointUrls['searchFriendFromOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_id',$list_id);
        $this->_httpClient->setPostParam('type','F');
        return $this->_getHttpClientResponse();;
    }
    public function searchTwitterFriends($list_id){
        $endpointUrl = $this->_endpointUrls['searchFriendFromOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_id',$list_id);
        $this->_httpClient->setPostParam('type','T');
        return $this->_getHttpClientResponse();;
    }
    public function registerLinktoFacebook($id,$token,$is_cancel){
        $endpointUrl = $this->_endpointUrls['registerLinkToOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('id',$id);
        $this->_httpClient->setPostParam('type','F');
        $this->_httpClient->setPostParam('token',$token);
        $this->_httpClient->setPostParam('secret',null);
        $this->_httpClient->setPostParam('is_cancel',$is_cancel);
        return $this->_getHttpClientResponse();;
    }
    public function registerLinktoTwitter($id,$token,$secret,$is_cancel){
        $endpointUrl = $this->_endpointUrls['registerLinkToOtherServices'];

        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('id',$id);
        $this->_httpClient->setPostParam('type','T');
        $this->_httpClient->setPostParam('token',$token);
        $this->_httpClient->setPostParam('secret',$secret);
        $this->_httpClient->setPostParam('is_cancel',$is_cancel);
        return $this->_getHttpClientResponse();;
    }
}

class MeshtilesException extends Exception {
}
