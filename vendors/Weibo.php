﻿<?php

/**
 * @author duythanh
 * @copyright 2012
 */
require_once 'CurlHttpClient.php';

class Weibo {

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
        'authorize' => 'https://api.weibo.com/oauth2/authorize?client_id=%s&redirect_uri=%s',
        'access_token' => 'https://api.weibo.com/oauth2/access_token',
        'update_status' =>'https://api.weibo.com/2/statuses/update.json',
        'status_upload' => 'https://upload.api.weibo.com/2/statuses/upload.json',
        /*
        'user' => 'https://api.instagram.com/v1/users/%d/?access_token=%s',
        'user_feed' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&max_id=%s&min_id=%s',
        'user_feed2' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&max_tag_id=%s',
        'user_recent' => 'https://api.instagram.com/v1/users/%d/media/recent/?access_token=%s&max_id=%s&count=20',
        'user_search' => 'https://api.instagram.com/v1/users/search?q=%s&access_token=%s',
        'user_follows' => 'https://api.instagram.com/v1/users/%d/follows?access_token=%s&cursor=%s',
        'user_followed_by' => 'https://api.instagram.com/v1/users/%d/followed-by?access_token=%s&cursor=%s',
        'user_requested_by' => 'https://api.instagram.com/v1/users/self/requested-by?access_token=%s',
        'user_relationship' => 'https://api.instagram.com/v1/users/%s/relationship?access_token=%s',
        'modify_user_relationship' => 'https://api.instagram.com/v1/users/%s/relationship?action=%s&access_token=%s',
        'media' => 'https://api.instagram.com/v1/media/%s?access_token=%s',
        'media_search' => 'https://api.instagram.com/v1/media/search?lat=%s&lng=%s&max_timestamp=%d&min_timestamp=%d&distance=%d&access_token=%s',
        'media_popular' => 'https://api.instagram.com/v1/media/popular?access_token=%s',
        'media_comments' => 'https://api.instagram.com/v1/media/%s/comments?access_token=%s',
        'post_media_comment' => 'https://api.instagram.com/v1/media/%s/comments?access_token=%s',
        'delete_media_comment' => 'https://api.instagram.com/v1/media/%d/comments?comment_id=%d&access_token=%s',
        'likes' => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'post_like' => 'https://api.instagram.com/v1/media/%s/likes',
        'remove_like' => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'tags' => 'https://api.instagram.com/v1/tags/%s?access_token=%s',
        'tags_recent' => 'https://api.instagram.com/v1/tags/%s/media/recent?max_id=%s&min_id=%s&access_token=%s',
        'tags_recent2' => 'https://api.instagram.com/v1/tags/%s/media/recent?max_tag_id=%s&access_token=%s',
        'tags_search' => 'https://api.instagram.com/v1/tags/search?q=%s&access_token=%s',
        'locations' => 'https://api.instagram.com/v1/locations/%d?access_token=%s',
        'locations_recent' => 'https://api.instagram.com/v1/locations/%s/media/recent/?max_id=%s&min_id=%s&max_timestamp=%s&min_timestamp=%s&access_token=%s',
        'locations_recent2' => 'https://api.instagram.com/v1/locations/%s/media/recent/?max_id=%s&access_token=%s',
        'locations_search' => 'https://api.instagram.com/v1/locations/search?lat=%s&lng=%s&foursquare_id=%d&distance=%d&access_token=%s', 
        */
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
        $this->_httpClient->setPostParam('client_id', $this->_config['client_id'],true);
        $this->_httpClient->setPostParam('client_secret', $this->_config['client_secret'],true);
        $this->_httpClient->setPostParam('grant_type', $this->_config['grant_type'],true);
        $this->_httpClient->setPostParam('redirect_uri', $this->_config['redirect_uri'],true);
        $this->_httpClient->setPostParam('code', $this->getAccessCode(),true);
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
        return $_GET[self::RESPONSE_CODE_PARAM];
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
            $this->_config['client_id'],
            $this->_config['redirect_uri']);

        header('Location: ' . $authorizationUrl);
        exit(1);
    }

    /**
      * Get basic information about a user.
      * @param $id
      */
    public function getUser($id) {
        $endpointUrl = sprintf($this->_endpointUrls['user'], $id, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * See the authenticated user's feed.
     * @param integer $maxId. Return media after this maxId.
     * @param integer $minId. Return media before this minId.
     */
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
    public function getUserRecent($id, $maxId = '') {
        $endpointUrl = sprintf($this->_endpointUrls['user_recent'], $id, $this->getAccessToken(),$maxId);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for a user by name.
     * @param string $name. A query string
     */
    public function searchUser($name) {
        $endpointUrl = sprintf($this->_endpointUrls['user_search'], $name, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get the list of users this user follows.
     * @param integer $id. The user id
     */
    public function getUserFollows($id,$cursor) {
        $endpointUrl = sprintf($this->_endpointUrls['user_follows'], $id, $this->getAccessToken(),$cursor);
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get the list of users this user is followed by.
     * @param integer $id
     */
    public function getUserFollowedBy($id,$cursor) {
        $endpointUrl = sprintf($this->_endpointUrls['user_followed_by'], $id, $this->getAccessToken(),$cursor);
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
        
        $endpointUrl = sprintf($this->_endpointUrls['media'], $id, $this->getAccessToken());
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
    public function getPopularMedia() {
        $endpointUrl = sprintf($this->_endpointUrls['media_popular'], $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Get a full list of comments on a media.
     * @param integer $id
     */
    public function getMediaComments($id) {
        $endpointUrl = sprintf($this->_endpointUrls['media_comments'], $id, $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }

    /**
     * Create a comment on a media.
     * @param integer $id
     * @param string $text
     */
    public function postMediaComment($id, $text) {
        //curl_init($text);
        $endpointUrl = sprintf($this->_endpointUrls['post_media_comment'], $id, $this->getAccessToken() );
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        return $this->_getHttpClientResponse();
    }
    public function postStatusUpdate($status){
        $endpointUrl = $this->_endpointUrls['update_status'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('access_token', $this->getAccessToken());
        $this->_httpClient->setPostParam('status', $status);
        return $this->_getHttpClientResponse();
        
    }
    public function postMediaStatus($status,$pic){
        $endpointUrl = $this->_endpointUrls['status_upload'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('access_token', $this->getAccessToken());
        $this->_httpClient->setPostParam('status', $status);
        $this->_httpClient->setPostParam('pic', '@'.$pic);
        var_dump($pic);
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
        $endpointUrl = sprintf($this->_endpointUrls['post_like'], $mediaId);
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('access_token', $this->getAccessToken());
        return $this->_getHttpClientResponse();
    }

    /**
     * Remove a like on this media by the currently authenticated user.
     * @param integer $mediaId
     */
    public function removeLike($mediaId) {
        $endpointUrl = sprintf($this->_endpointUrls['remove_like'], $mediaId, $this->getAccessToken());
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
    public function getRecentTags($tagName, $maxId = '', $minId = '') {
        $endpointUrl = sprintf($this->_endpointUrls['tags_recent2'], $tagName, $maxId, $this->getAccessToken());
        //var_dump($endpointUrl);
        $this->_initHttpClient($endpointUrl);
        //var_dump($this->_config);
        return $this->_getHttpClientResponse();
    }

    /**
     * Search for tags by name - results are ordered first as an exact match, then by popularity.
     * @param string $tagName
     */
    public function searchTags($tagName) {
        $endpointUrl = sprintf($this->_endpointUrls['tags_search'], urlencode($tagName), $this->getAccessToken());
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
    public function getLocationRecentMedia($id, $maxId = '', $minId = '', $maxTimestamp = '', $minTimestamp = '') {
        $endpointUrl = sprintf($this->_endpointUrls['locations_recent2'], $id, $maxId, $this->getAccessToken());
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
}

class WeiboException extends Exception {
}


?>