<?php 

return array(
    'secretKey'     => '7h3S3cr37H1s70ry0f1GIn73gra710n', // used to encrypt the token, you must create your custom (random) string
    'appId'         => '522973819541195',
    'appSecret'     => '498c5e8f2b564afcd11f0275416b4aae',
    'redirectUri'   => 'https://selfieym.com/instagram-basic-display/',

    'limit_per_page'=> 8,

    'exclude'       => array(
        // list of names of IG media to exclude, e.g. '1262629994_225747615234351_9142654999463375560_n.jpg',
    ),

    'debug'         => true,
    'token_file'    => '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'token.json',
);
