<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles global data across the request.
 * 
 */
class SliceWP_Globals {

    /**
     * The globals data set.
     * 
     * @access protected
     * @var    array
     * 
     */
    protected $data = array();

    /**
     * Constructor.
     * 
     */
    public function __construct() {}

    /**
     * Globals setter.
     * 
     * @param string $key
     * @param mixed  $value
     * 
     */
    public function set( $key, $value ) {

        $this->data[$key] = $value;

    }

    /**
     * Globals getter.
     * 
     * @param string $key
     * @param mixed  $default
     * 
     */
    public function get( $key, $default = null ) {

        return ( isset( $this->data[$key] ) ? $this->data[$key] : $default );

    }

}