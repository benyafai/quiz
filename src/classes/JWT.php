<?php
class JWT {

    protected $secret;

    public function __construct() {
        $this->secret = getenv('JWT_SECRET');
    }

    public function sign( $input, $algorithm = "HS256" ) {
        $header = base64_encode(json_encode([
            "alg" => $algorithm,
            "typ" => "JWT",
        ]));
        $payload = base64_encode(json_encode( $input ));
        $signature = $this->signature($header, $payload);
        return $header.".".$payload.".".$signature;
    }

    public function verify( $input ) {
        if ( !$input ) {
            return false;
        }
        $data = explode(".", $input);
        $computed = $this->signature($data[0], $data[1] );
        if ( !$computed ) {
            return false;
        }
        if ( !hash_equals( $data[2], $computed) ) {
            return false;
        }
        return json_decode(base64_decode($data[1]));
    }

    private function signature( $header, $payload ) {
        $params = json_decode(base64_decode($header));
        if ( !isset($params->alg) ) {
            return false;
        }
        switch ( $params->alg ) {
            case "HS256":
                return hash_hmac("SHA256", $header.".".$payload, $this->secret);
            case "HS384":
                return hash_hmac("SHA384", $header.".".$payload, $this->secret);
            case "HS512":
                return hash_hmac("SHA512", $header.".".$payload, $this->secret);
            default:
                return false;
        }
    }
}