<?php
/**
 * This class is based on the class originally developed by David Billingham
 * and accessible at http://twitter.slawcup.com/twitter.class.phps
 * @version 0.1
 * @package PHPTwitterAPI
 */
class TwitterSearch {
    /**
     * Can be set to JSON (requires PHP 5.2 or the json pecl module) or XML - json|xml
     * @var string
     */
    var $type = 'json';

    /**
     * It is unclear if Twitter header preferences are standardized, but I would suggest using them.
     * More discussion at http://tinyurl.com/3xtx66
     * @var array
     */
    var $headers=array('X-Twitter-Client: PHPTwitterSearch','X-Twitter-Client-Version: 0.1','X-Twitter-Client-URL: http://lab.jixia.org/');

    /**
     * Recommend setting a user-agent so Twitter knows how to contact you inc case of abuse. Include your email
     * @var string
     */
    var $user_agent='';

    /**
     * @var string
     */
    var $query='';

    /**
     * @var array
     */
    var $responseInfo=array();

    var $userid = '';

    /**
    * @param string $query optional
    */
    function TwitterSearch($query=false) {
        $this->query = $query;
    }

    /**
    * Build and perform the query, return the results.
    * @param $reset_query boolean optional.
    * @return object
    */
    function results($userid) {
        $request  = 'http://api.twitter.com/statuses/user_timeline/' . $userid . '.' . $this->type;

        return $this->objectify($this->process($request));
    }

    /**
     * Internal function where all the juicy curl fun takes place
     * this should not be called by anything external unless you are
     * doing something else completely then knock youself out.
     * @access private
     * @param string $url Required. API URL to request
     * @param string $postargs Optional. Urlencoded query string to append to the $url
     */
    function process($url, $postargs=false) {
        $ch = curl_init($url);
        if($postargs !== false) {
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);

        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);

        if( intval( $this->responseInfo['http_code'] ) == 200 )
            return $response;
        else
            return false;
    }

    /**
     * Function to prepare data for return to client
     * @access private
     * @param string $data
     */
    function objectify($data) {
        if( $this->type ==  'json' )
            return (object) json_decode($data);

        else if( $this->type == 'xml' ) {
            if( function_exists('simplexml_load_string') ) {
                $obj = simplexml_load_string( $data );

                $statuses = array();
                foreach( $obj->status as $status ) {
                    $statuses[] = $status;
                }
                return (object) $statuses;
            }
            else {
                return $out;
            }
        }
        else
            return false;
    }
}

?>
