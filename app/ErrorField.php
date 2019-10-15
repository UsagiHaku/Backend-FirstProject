<?php


namespace App;


class ErrorField
{
    public $code;
    public $title;

    function __construct($code, $title) {
        $this->code = $code;
        $this->title = $title;
    }
}
