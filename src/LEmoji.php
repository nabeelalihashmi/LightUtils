<?php

namespace IconicCodes\LightUtils;

class LEmoji {

    // array of all :code: and emoji at once
    public $emoji = array(
        ':grinning:' => '😀',
        ':grin:' => '😁',
        ':joy:' => '😂',
        ':smiley:' => '😃',
        ':smile:' => '😄',
        ':sweat_smile:' => '😅',
        ':laughing:' => '😆',
        ':wink:' => '😉',
        ':blush:' => '😊',
        ':yum:' => '😋',
        ':sunglasses:' => '😎',
        ':heart_eyes:' => '😍',
        ':kissing_heart:' => '😘',
        ':kissing_closed_eyes:' => '😚'
    );


    // replace all :emoji: with the actual emoji using regex
    public function replace($text) {
        foreach ($this->emoji as $key => $value) {
            $text = preg_replace('/' . $key . '/', $value, $text);
        }
        return $text;
    }
}