<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    /**
     * JWT Token generator
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    public function generate(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800
    ): string {
        // 10800= 3 heures
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // encoder en base 64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // nettoyer valeur encoder
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // signer le token
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true);

        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // create token
        $jwt = "$base64Header.$base64Payload.$base64Signature";

        return $jwt;
    }

    public function isValid(string $token): bool
    {
        // check if token is valid
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    // check payload
    public function getPayload(string $token): array
    {
        // recuperer le payload
        $array = explode('.', $token);

        // decode
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }
    public function getHeader(string $token): array
    {
        // recuperer le payload
        $array = explode('.', $token);

        // decode
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // check if token is expired
    public function isExpired(string $token): bool
    {
        // check if token is expired
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // check if signature is valid
    public function check(string $token, string $secret): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        $verifToken = $this->generate(
            $header,
            $payload,
            $secret,
            0 // no validity
        );

        return $token === $verifToken;
    }
}