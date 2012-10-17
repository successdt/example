<?php

/**
 * @author duythanh
 * @copyright 2012
 */
 
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
        'approvedOrRejectContactRequest' => 'http://107.20.246.0/api/User/approvedOrRejectRequestContact',
        'blockUser'=>'http://107.20.246.0/api/View/blockUser?current_app_key=%s&app_secret=%s&access_token=%s&blocker_id=%s&is_block=%s',
        'buttonClick' => 'http://107.20.246.0/api/View/buttonClick?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'cancelLikePhoto'=>'http://107.20.246.0/api/View/cancelLikePhoto?app_key=%s&app_secret=%s&access_token=%s&&photo_id=%s',
        'checkConnectionStatus' => 'http://107.20.246.0/api/Start/checkConnectionStatus',
        'checkUserIsBlocked'=>'http://107.20.246.0/api/View/checkUserIsBlocked.html?app_key=%s&app_secret=%s&access_token=%s&blocker_id=%s',
        'clientsPromotion'=>'http://107.20.246.0/api/SignUp/checkStatusOnClientPromotion',
        'checkNoticeStatus'=>'http://107.20.246.0/api/User/checkNoticeStatus?last_time=%s&type_OS=%s&language_OS=%s',
        'checkPrivacy'=>'http://107.20.246.0/api/User/checkPrivacy?app_key=%s&app_secret=%s&access_token=%s',
        'checkUserNameAndEmail'=>'http://107.20.246.0/api/SignUp/checkUserNameAndEmail?user_name=%s&email=%s',
        'createNewsForFriendFromOtherService'=>'http://107.20.246.0/api/SignUp/createNewsForFriendFromOtherService',
        'deleteComment'=>'http://107.20.246.0/api/View/deleteComment?app_key=%s&app_secret=%s&access_token=%s&comment_id=%s',
        'deletePhoto'=>'http://107.20.246.0/api/View/deletePhoto?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'editCaption'=>'http://107.20.246.0/api/Photo/editCaption',
        'getAllPennants'=>'http://107.20.246.0/api/User/getAllPennants',
        'getCommentOfPhoto'=>'http://107.20.246.0/api/View/getCommentOfPhoto?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'getDescriptionNewsByID'=>'http://107.20.246.0/api/User/getDescriptionNewsByID?notice_id=%s&type_OS=%s&language_OS=%s',
        'getFacebookAccessToken'=>'http://107.20.246.0/api/Photo/getFacebookAccessToken',
        'getFavouristTags'=>'http://107.20.246.0/api/Photo/getFavouristTags?app_key=%s&app_secret=%s&access_token=%s',
        'getFrequentTags'=>'http://107.20.246.0/api/Photo/getFrequentTags?app_key=%s&app_secret=%s&access_token=%s',
        'getInfoWhenLogout'=>'http://107.20.246.0/api/Login/getInfoWhenLogout?app_key=%s&app_secret=%s&access_token=%s',
        'getLastActivityTime'=>'http://107.20.246.0/api/User/getLastActivityTime?app_key=%s&app_secret=%s&access_token=%s',
        'getListContactRequest'=>'http://107.20.246.0/api/User/getListContactRequest?app_key=%s&app_secret=%s&access_token=%s',
        'getListCountry'=>'http://107.20.246.0/api/SignUp/getListCountry',
        'getListFilterLocked'=>'http://107.20.246.0/api/Photo/getListFilterLocked?app_key=%s&app_secret=%s&access_token=%s',
        'getListPennantsByUser'=>'http://107.20.246.0/api/User/getListPennantsByUser?app_key=%s&app_secret=%s&access_token=%s',
        'getListPhotoByLocationID'=>'http://107.20.246.0/api/View/getListPhotoByLocationID?app_key=%s&app_secret=%s&access_token=%s&location_id=%s&page_index=%s',
        'getListPhotoByTags'=>'http://107.20.246.0/api/View/getListPhotoByTags?app_key=%s&app_secret=%s&access_token=%s&tag=%s&page_index=%d',
        'getListPhotoByYOU'=>'http://107.20.246.0/api/View/getListPhotoByYOU?app_key=%s&app_secret=%s&access_token=%s&page_index=%s',
        'getListPhotoDetail'=>'http://107.20.246.0/api/View/getListPhotoDetail',
        'getListPopularPhoto'=>'http://107.20.246.0/api/View/getListPopularPhoto?app_key=%s&app_secret=%s&access_token=%s&page_index=%s',
        'getListTagsRecommend'=>' http://107.20.246.0/api/Photo/getListTagsRecommend?app_key=%s&app_secret=%s&access_token=%s&keyword=%s', 
        'getListTrendTags'=>'http://107.20.246.0/api/Trend/getListTrendTags',
        'getListUserByTitleAndTag'=>'http://107.20.246.0/api/Trend/getListUserByTitleAndTag',
        'getListUserByContactAddress'=>'http://107.20.246.0/api/SignUp/getListUserByContactAddress',
        'getListUserByUserName'=>'http://107.20.246.0/api/SignUp/getListUserByUserName',
        'getListUserFollow'=>'http://107.20.246.0/api/View/getListUserFollow?app_key=%s&app_secret=%s&access_token=%s&viewed_user_id=%s&is_following=%s&page_index=%s',
        'getListUserPhotoByTags'=>'http://107.20.246.0/api/Trend/getListUserPhotoByTags?app_key=%s&app_secret=%s&access_token=%s&tag=%s&page_index=%d',
        'getListUserRatingPhoto'=>'http://107.20.246.0/View/getListUserRatingPhoto?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s&page_index=%d',
        'getListUserRecommended'=>'http://107.20.246.0/api/SignUp/getListUserRecommended',
        'getListUserTitleDetail'=>'http://107.20.246.0/api/View/getListUserTitleDetail?app_key=%s&app_secret=%s&access_token=%s&&title=%s&page_index=%s',
        'getNewsOfFavourisTags'=>'http://107.20.246.0/api/User/getNewsOfFavourisTags?app_key=%s&app_secret=%s&access_token=%s',
        'getNotification'=>'http://107.20.246.0/api/User/getNotification?app_key=%s&app_secret=%s&access_token=%s',
        'getPennantsDetail'=>'http://107.20.246.0/api/User/getPennantsDetail?app_key=%s&app_secret=%s&access_token=%s&pennant_id=%s&language=%s',
        'getPhotoDetail'=>'http://107.20.246.0/api/View/getPhotoDetail?app_key=%s&app_secret=%s&access_token=%s&photo_id=%s',
        'getPhotoLinkToOtherServices'=>'http://107.20.246.0/api/View/getPhotoLinkToOtherServices?photo_id=%s',
        'getPhotoOfUser'=>' http://107.20.246.0/api/View/getPhotoOfUser?app_key=%s&app_secret=%s&access_token=%s&viewed_id=%s&page_index=%s',
        'getTimelinePhotoOfUserFollowing'=>'http://107.20.246.0/api/Follow/getTimelinePhotoOfUserFollowing',
        'getTrendTagDetail'=>'http://107.20.246.0/api/Trend/getTrendTagDetail',
        'getYOUData'=>'http://107.20.246.0/a/api/User/getYOUData?app_key=%s&app_secret=%s&access_token=%s',
        'getUserAccesToken'=>'http://107.20.246.0/getUserAccesToken?app_key=%s&app_secret=%s&access_token=%s',
        'getUserProfile'=>'http://107.20.246.0/a/api/User/getUserProfile?app_key=%s&app_secret=%s&access_token=%s',
        'getUserNews'=>'http://107.20.246.0/api/User/getUserNews?app_key=%s&app_secret=%s&access_token=%s',
        'getUserStatus'=>'http://107.20.246.0//api/User/getUserStatus?app_key=%s&app_secret=%s&access_token=%s',
        'getUserViewDetail'=>'http://107.20.246.0//api/View/getUserViewDetail?app_key=%s&app_secret=%s&access_token=%s&viewed_user_id',
        'listNoticeNews'=>'http://107.20.246.0/api/User/listNoticeNews?page_index=%s&type_OS=%s&language_OS=%s',
        'login'=>'http://107.20.246.0/api/Login/login',
        'logout'=>'http://107.20.246.0/api/User/logout?app_key=%s&app_secret=%s&access_token=%s',
        'logoutAccount'=>'http://107.20.246.0/api/User/logoutAccount?app_key=%s&app_secret=%s&access_token=%s',
        'postPhoto'=>'http://107.20.246.0/api/Photo/postPhoto',
        'postPhotoFirst'=>' http://107.20.246.0/api/Photo/postPhotoFirst',
        'postPhotoSecond'=>' http://107.20.246.0/api/Photo/postPhotoSecond',
        'postComment'=>'http://107.20.246.0/api/View/postComment',
        'registerFollowingUser'=>'http://107.20.246.0/api/SignUp/registerFollowingUser',
        'registerLinkToOtherServices'=>'http://107.20.246.0/api/SignUp/registerLinkToOtherServices',
        'registerNotification'=>'http://107.20.246.0/api/User/registerNotification',
        'registerPhotoLinkToOtherServices'=>'http://107.20.246.0/api/Photo/registerPhotoLinkToOtherServices',
        'registerPrivacy'=>'http://107.20.246.0/api/User/registerPrivacy?app_key=%s&app_secret=%s&access_token=%s&is_privacy=%s',
        'registerYOUData'=>'http://107.20.246.0/api/SignUp/registerYOUData',
        'registerYOurTags'=>'http://107.20.246.0/api/SignUp/registerYOurTags',
        'report'=>'http://107.20.246.0/api/View/report',
        'reportErrors'=>'http://107.20.246.0/api/Start/reportErrors',
        'resetPassword'=>'http://107.20.246.0/api/Login/resetPassword?user_name=%s&language=%s',
        'searchFriendFromOtherServices'=>'http://107.20.246.0/api/SignUp/searchFriendFromOtherServices',
        'signUp'=>'http://107.20.246.0/api/SignUp/signUp',
        'updateImageProfile'=>'http://107.20.246.0/api/SignUp/updateImageProfile',
        'updateUserProfile'=>'http://107.20.246.0/api/User/updateUserProfile'
        
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
    public function approvedOrRejectContactRequest($requester_id,$requester_id){
        $endpointUrl = $this->_endpointUrls['approvedOrRejectContactRequest'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('requester_id',$requester_id);
        $this->_httpClient->setPostParam('requester_id',$requester_id);
        return $this->_getHttpClientResponse();;
    }
    public function blockUser($current_user_id,$blocker_id,$is_block) {
        $endpointUrl = sprintf($this->_endpointUrls['blockUser'],
            $current_user_id,
            $blocker_id,
            $is_block);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function buttonClick($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['buttonClick'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function cancelLikePhoto($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['cancelLikePhoto'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }    
    public function checkConnectionStatus(){
        $endpointUrl = $this->_endpointUrls['checkConnectionStatus'];
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function checkUserIsBlocked($blocker_id) {
        $endpointUrl = sprintf($this->_endpointUrls['checkUserIsBlocked'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $blocker_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function clientsPromotion(){
        $endpointUrl = $this->_endpointUrls['clientsPromotion'];
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function checkNoticeStatus($last_time,$type_OS,$language_OS) {
        $endpointUrl = sprintf($this->_endpointUrls['checkNoticeStatus'],
            $last_time,
            $type_OS,
            $language_OS);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function checkPrivacy($blocker_id) {
        $endpointUrl = sprintf($this->_endpointUrls['checkPrivacy'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function checkUserNameAndEmail($user_name,$email) {
        $endpointUrl = sprintf($this->_endpointUrls['checkUserNameAndEmail'],
            $user_name,
            $user_name);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function createNewsForFriendFromOtherService($list_id,$type,$friend_name){
        $endpointUrl = $this->_endpointUrls['createNewsForFriendFromOtherService'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_id',$list_id);
        $this->_httpClient->setPostParam('type',$type);
        $this->_httpClient->setPostParam('friend_name',$friend_name);
        return $this->_getHttpClientResponse();;
    }
    public function deleteComment($comment_id) {
        $endpointUrl = sprintf($this->_endpointUrls['deleteComment'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $comment_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }  
    public function deletePhoto($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['deletePhoto'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function editCaption($photo_id,$caption){
        $endpointUrl = $this->_endpointUrls['editCaption'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('photo_id',$photo_id);
        $this->_httpClient->setPostParam('caption ',$caption);
        return $this->_getHttpClientResponse();;
    }
    public function getAllPennants(){
        $endpointUrl = $this->_endpointUrls['getAllPennants'];
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getCommentOfPhoto($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['getCommentOfPhoto'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getDescriptionNewsByID($notice_id,$type_OS,$language_OS) {
        $endpointUrl = sprintf($this->_endpointUrls['getDescriptionNewsByID'],
            $notice_id,
            $type_OS,
            $language_OS);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getFacebookAccessToken(){
        $endpointUrl = $this->_endpointUrls['getFacebookAccessToken'];
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }    
    public function getFavouristTags() {
        $endpointUrl = sprintf($this->_endpointUrls['getFavouristTags'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getFrequentTags() {
        $endpointUrl = sprintf($this->_endpointUrls['getFrequentTags'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getInfoWhenLogout() {
        $endpointUrl = sprintf($this->_endpointUrls['getInfoWhenLogout'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getLastActivityTime() {
        $endpointUrl = sprintf($this->_endpointUrls['getLastActivityTime'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListContactRequest() {
        $endpointUrl = sprintf($this->_endpointUrls['getListContactRequest'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListCountry(){
        $endpointUrl = $this->_endpointUrls['getListCountry'];
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListFilterLocked() {
        $endpointUrl = sprintf($this->_endpointUrls['getListFilterLocked'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListPennantsByUser() {
        $endpointUrl = sprintf($this->_endpointUrls['getListPennantsByUser'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }    
    public function getListPhotoByLocationID($location_id,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListPhotoByLocationID'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $location_id,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }    
    public function getListPhotoByTags($tag,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListPhotoByTags'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $tag,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListPhotoByYOU($page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListPhotoByYOU'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }      
    public function getListPhotoDetail($list_photo_id){
        $endpointUrl = $this->_endpointUrls['getListPhotoDetail'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_photo_id',$list_photo_id);
        return $this->_getHttpClientResponse();;
    }
    public function getListPopularPhoto($page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListPopularPhoto'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListTagsRecommend($keyword) {
        $endpointUrl = sprintf($this->_endpointUrls['getListTagsRecommend'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $keyword);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }        
    public function getListTrendTags($is_weekly ,$page_index){
        $endpointUrl = $this->_endpointUrls['getListTrendTags'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('is_weekly',$is_weekly);
        $this->_httpClient->setPostParam('page_index',$page_index);
        return $this->_getHttpClientResponse();;
    }
    
    public function getListUserByTitleAndTag($tag,$page_index){
        $endpointUrl = $this->_endpointUrls['getListUserByTitleAndTag'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('tag ',$tag);
        $this->_httpClient->setPostParam('page_index',$page_index);
        return $this->_getHttpClientResponse();;
    }
    public function getListUserByContactAddress($list_mobile_phone,$list_email ){
        $endpointUrl = $this->_endpointUrls['getListUserByContactAddress'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_mobile_phone',$list_mobile_phone);
        $this->_httpClient->setPostParam('list_email',$list_email);
        return $this->_getHttpClientResponse();;
    }
    public function getListUserByUserName($keyword,$page_index){
        $endpointUrl = $this->_endpointUrls['getListUserByUserName'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('keyword ',$keyword);
        $this->_httpClient->setPostParam('page_index',$page_index);
        return $this->_getHttpClientResponse();;
    }
    public function getListUserFollow($viewed_user_id,$is_following,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListUserFollow'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $viewed_user_id,
            $is_following,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListUserPhotoByTags($tag,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListUserPhotoByTags'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $tag,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListUserRatingPhoto($photo_id,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListUserRatingPhoto'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getListUserRecommended($page_index){
        $endpointUrl = $this->_endpointUrls['getListUserRecommended'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('page_index',$page_index);
        return $this->_getHttpClientResponse();;
    }
    public function getListUserTitleDetail($title,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getListUserTitleDetail'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $title,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getNewsOfFavourisTags() {
        $endpointUrl = sprintf($this->_endpointUrls['getNewsOfFavourisTags'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getNotification() {
        $endpointUrl = sprintf($this->_endpointUrls['getNotification'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getPennantsDetail($pennant_id,$language) {
        $endpointUrl = sprintf($this->_endpointUrls['getPennantsDetail'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $pennant_id,
            $language);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getPhotoDetail($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['getPhotoDetail'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getPhotoLinkToOtherServices($photo_id) {
        $endpointUrl = sprintf($this->_endpointUrls['getPhotoLinkToOtherServices'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $photo_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getPhotoOfUser($viewed_id,$page_index) {
        $endpointUrl = sprintf($this->_endpointUrls['getPhotoOfUser'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $viewed_id,
            $page_index);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getTimelinePhotoOfUserFollowing($page_index){
        $endpointUrl = $this->_endpointUrls['getTimelinePhotoOfUserFollowing'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('page_index',$page_index);
        return $this->_getHttpClientResponse();;
    } 
    public function getTrendTagDetail($tag){
        $endpointUrl = $this->_endpointUrls['getTrendTagDetail'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('tag',$tag);
        return $this->_getHttpClientResponse();;
    }
    public function getYOUData() {
        $endpointUrl = sprintf($this->_endpointUrls['getYOUData'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserAccesToken() {
        $endpointUrl = sprintf($this->_endpointUrls['getUserAccesToken'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserProfile() {
        $endpointUrl = sprintf($this->_endpointUrls['getUserProfile'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserNews() {
        $endpointUrl = sprintf($this->_endpointUrls['getUserNews'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function getUserViewDetail($viewed_user_id) {
        $endpointUrl = sprintf($this->_endpointUrls['getUserViewDetail'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $viewed_user_id);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function listNoticeNews($page_index,$type_OS,$language_OS) {
        $endpointUrl = sprintf($this->_endpointUrls['listNoticeNews'],
            $page_index,
            $type_OS,
            $language_OS);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function login($user_name,$password,$device_token){
        $endpointUrl = $this->_endpointUrls['login'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('user_name',$user_name);
        $this->_httpClient->setPostParam('password',$password);
        $this->_httpClient->setPostParam('device_token',$device_token);
        return $this->_getHttpClientResponse();;
    }
    public function logout($viewed_user_id) {
        $endpointUrl = sprintf($this->_endpointUrls['logout'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function logoutAccount($viewed_user_id) {
        $endpointUrl = sprintf($this->_endpointUrls['logoutAccount'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken());
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function postPhoto($list_tags,$button_type, $latitude, $longitude, $comment, $photo_object, $filter_id, $location_latitude, $location_longitude, $llocation_text, $location_id, $model, $timezone, $friend){
        $endpointUrl = $this->_endpointUrls['postPhoto'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_tags',$list_tags);
        $this->_httpClient->setPostParam('button_type',$button_type);
        $this->_httpClient->setPostParam('longitude',$longitude);
        $this->_httpClient->setPostParam('latitude',$latitude);
        $this->_httpClient->setPostParam('comment',$comment);
        $this->_httpClient->setPostParam('photo_object',$photo_object);
        $this->_httpClient->setPostParam('filter_id ',$filter_id );
        $this->_httpClient->setPostParam('location_latitude ',$location_latitude );
        $this->_httpClient->setPostParam('location_longitude',$location_longitude);
        $this->_httpClient->setPostParam('location_text',$llocation_text);
        $this->_httpClient->setPostParam('location_id',$location_id);
        $this->_httpClient->setPostParam('model',$model);
        $this->_httpClient->setPostParam('timezone',$timezone);
        $this->_httpClient->setPostParam('friend',$friend);
        return $this->_getHttpClientResponse();;
    }
    public function postPhotoFirst($photo_object){
        $endpointUrl = $this->_endpointUrls['postPhotoFirst'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('photo_object',$photo_object);
        return $this->_getHttpClientResponse();;
    }
    public function postPhotoSecond($photo_id, $list_tags,$button_type, $latitude, $longitude, $comment, $filter_id, $location_latitude, $location_longitude, $location_text, $location_id, $model, $timezone, $friend){
        $endpointUrl = $this->_endpointUrls['postPhotoSecond'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('photo_id',$photo_id);
        $this->_httpClient->setPostParam('list_tags',$list_tags);
        $this->_httpClient->setPostParam('button_type',$button_type);
        $this->_httpClient->setPostParam('longitude',$longitude);
        $this->_httpClient->setPostParam('latitude',$latitude);
        $this->_httpClient->setPostParam('comment',$comment);
        $this->_httpClient->setPostParam('filter_id ',$filter_id );
        $this->_httpClient->setPostParam('location_latitude ',$location_latitude );
        $this->_httpClient->setPostParam('location_longitude',$location_longitude);
        $this->_httpClient->setPostParam('location_text',$location_text);
        $this->_httpClient->setPostParam('location_id',$location_id);
        $this->_httpClient->setPostParam('model',$model);
        $this->_httpClient->setPostParam('timezone',$timezone);
        $this->_httpClient->setPostParam('friend',$friend);
        return $this->_getHttpClientResponse();;
    }
    public function postComment($photo_id, $comment){
        $endpointUrl = $this->_endpointUrls['postComment'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('photo_id',$photo_id);
        $this->_httpClient->setPostParam('comment',$comment);
        return $this->_getHttpClientResponse();;
    }
    public function registerFollowingUser($following_user_id, $is_following){
        $endpointUrl = $this->_endpointUrls['registerFollowingUser'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('following_user_id',$following_user_id);
        $this->_httpClient->setPostParam('is_following',$is_following);
        return $this->_getHttpClientResponse();;
    }
   public function registerLinkToOtherServices($type = F,$id,$token = null,$secret = null,$is_cancel = 0){
        $endpointUrl = $this->_endpointUrls['registerLinkToOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('type',$type);
        $this->_httpClient->setPostParam('id',$id);
        $this->_httpClient->setPostParam('token',$token);
        $this->_httpClient->setPostParam('secret',$secret);
        $this->_httpClient->setPostParam('is_cancel',$is_cancel);
        return $this->_getHttpClientResponse();;
    }
    public function registerNotification(){
        /**
         * Not available for web
         */
    }
    public function registerPhotoLinkToOtherServices($photo_id,$link_facebook = 1,$link_twitter = 1,$link_tumblr = 1,$link_weibo = 1,$link_flickr = 1){
        $endpointUrl = $this->_endpointUrls['registerPhotoLinkToOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('photo_id',$photo_id);
        $this->_httpClient->setPostParam('link_facebook',$link_facebook);
        $this->_httpClient->setPostParam('link_twitter ',$link_twitter  ); 
        $this->_httpClient->setPostParam('link_tumblr',$link_tumblr);
        $this->_httpClient->setPostParam('link_weibo',$link_weibo);
        $this->_httpClient->setPostParam('link_flickr',$link_flickr);
        return $this->_getHttpClientResponse();;
    }
    public function registerPrivacy($is_privacy = 0) {
        $endpointUrl = sprintf($this->_endpointUrls['registerPrivacy'],
            $this->_config['app_key'],
            $this->_config['app_secret'],
            $this->getAccessToken(),
            $is_privacy);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function registerYOUData($male, $interested,$current_city, $current_country, $blood_type, $birthday, $public_birthday, $public_gender, $latitude, $longitude){
        $endpointUrl = $this->_endpointUrls['registerYOUData'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('male',$male);
        $this->_httpClient->setPostParam('interested',$interested);
        $this->_httpClient->setPostParam('current_city ',$current_city );
        $this->_httpClient->setPostParam('current_country',$current_country);
        $this->_httpClient->setPostParam('blood_type',$blood_type);
        $this->_httpClient->setPostParam('birthday',$birthday);
        $this->_httpClient->setPostParam('public_birthday',$public_birthday );
        $this->_httpClient->setPostParam('public_gender',$public_gender);
        $this->_httpClient->setPostParam('longitude',$longitude);
        $this->_httpClient->setPostParam('latitude',$latitude);
        return $this->_getHttpClientResponse();;
    }
    public function registerYOurTags($list_tags){
        $endpointUrl = $this->_endpointUrls['registerYOurTags'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_tags ',$list_tags );
        return $this->_getHttpClientResponse();;
    }
    public function report($report_type, $report_message, $user_name_reported, $photo_id_reported, $photo_name_reported){
        $endpointUrl = $this->_endpointUrls['report'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('report_type',$report_type);
        $this->_httpClient->setPostParam('report_message',$report_message);
        $this->_httpClient->setPostParam('user_name_reported ',$user_name_reported );
        $this->_httpClient->setPostParam('photo_id_reported',$photo_id_reported);
        $this->_httpClient->setPostParam('photo_name_reported',$photo_name_reported);
        return $this->_getHttpClientResponse();;
    }
    public function reportErrors($log_file, $level, $title_error){
        $endpointUrl = $this->_endpointUrls['reportErrors'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('log_file ',$log_file );
        $this->_httpClient->setPostParam('level',$level);
        $this->_httpClient->setPostParam('title_error',$title_error );
        return $this->_getHttpClientResponse();;
    }
    public function resetPassword($user_name, $language) {
        $endpointUrl = sprintf($this->_endpointUrls['resetPassword'],
            $user_name,
            $language);
        $this->_initHttpClient($endpointUrl);
        return $this->_getHttpClientResponse();
    }
    public function searchFriendFromOtherServices($list_id, $type){
        $endpointUrl = $this->_endpointUrls['searchFriendFromOtherServices'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('list_id',$list_id);
        $this->_httpClient->setPostParam('type',$type);
        return $this->_getHttpClientResponse();;
    }
    public function signUp($user_name, $password, $email, $phone, $image, $first_name, $last_name, $male, $interested, $current_city, $current_country, $blood_type, $birthday, $public_birthday, $public_gender, $longitude, $latitude ){
        $endpointUrl = $this->_endpointUrls['signUp'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('user_name',$user_name);
        $this->_httpClient->setPostParam('password',$password);
        $this->_httpClient->setPostParam('email',$email);
        $this->_httpClient->setPostParam('phone',$phone);
        $this->_httpClient->setPostParam('image',$image);
        $this->_httpClient->setPostParam('first_name',$first_name);
        $this->_httpClient->setPostParam('last_name',$last_name);
        $this->_httpClient->setPostParam('model','Meshtiles web');
        $this->_httpClient->setPostParam('male',$male);
        $this->_httpClient->setPostParam('interested',$interested);
        $this->_httpClient->setPostParam('current_city ',$current_city );
        $this->_httpClient->setPostParam('current_country',$current_country);
        $this->_httpClient->setPostParam('blood_type',$blood_type);
        $this->_httpClient->setPostParam('birthday',$birthday);
        $this->_httpClient->setPostParam('public_birthday',$public_birthday );
        $this->_httpClient->setPostParam('public_gender',$public_gender);
        $this->_httpClient->setPostParam('longitude',$longitude);
        $this->_httpClient->setPostParam('latitude',$latitude);
        return $this->_getHttpClientResponse();;
    }
    public function updateImageProfile($image){
        $endpointUrl = $this->_endpointUrls['updateImageProfile'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('image',$image);
        return $this->_getHttpClientResponse();;
    }
    public function updateUserProfile( $user_name, $email, $password, $phone, $first_name, $last_name, $about){
        $endpointUrl = $this->_endpointUrls['updateUserProfile'];
        $this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
        $this->_httpClient->setPostParam('app_key',$this->_config['app_key']);
        $this->_httpClient->setPostParam('app_secret',$this->_config['app_secret']);
        $this->_httpClient->setPostParam('access_token',$this->getAccessToken());
        $this->_httpClient->setPostParam('user_name',$user_name);
        $this->_httpClient->setPostParam('email',$email);
        $this->_httpClient->setPostParam('password ',$password );
        $this->_httpClient->setPostParam('phone',$phone);
        $this->_httpClient->setPostParam('first_name',$first_name);
        $this->_httpClient->setPostParam('last_name',$last_name);
        $this->_httpClient->setPostParam('about',$about);
        return $this->_getHttpClientResponse();;
    }                                                    
}
?>