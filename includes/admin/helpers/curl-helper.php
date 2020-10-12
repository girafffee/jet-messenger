<?php


namespace JET_MSG\Admin\Helpers;


class Curl_Helper
{

    public $curl;
    public $url;
    public $fields;
    public $result;
    public $options;

    public function __construct( $url ) {
        $this->curl = curl_init();
        $this->url  = $url;
    }

    public function fields( $fields ) {
        $this->fields = $fields;

        return $this;
    }

    public function set_options() {
        $this->options = [
            CURLOPT_URL => $this->url,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $this->fields,
        ];

        return $this;
    }

    public function execute() {
        $this->set_options();

        curl_setopt_array( $this->curl, $this->options );
        $this->result = curl_exec( $this->curl );

        return $this->result;
    }

}