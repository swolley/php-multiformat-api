<?php

class Hal {
    public static function getHal() {
        return [
            "_links" => [
                "self" => ["href" => null], //path
                "next" => ["href" => null]  //path
            ]
        ];
    }
}