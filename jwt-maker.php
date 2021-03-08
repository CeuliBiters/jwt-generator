<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once('../../plugins/JWT-Framework/vendor/autoload.php'); //composer require web-token/jwt-framework

use Jose\Component\KeyManagement\JWKFactory;
use Jose\Easy\Build;
use Jose\Easy\Load;

echo "<h1>JSON Web Signature (JWS)</h1>";
/* Pembangkit Kunci Signatur */
$private_key = JWKFactory::createOCTKey(
    4096, // Size in bits of the key. We recommend at least 2048 bits.
    [
        'alg' => 'HS512', 
        'use' => 'sig',   
        'kid' => "test",
    ]
);
echo "<h2>Private Key</h2>";
echo "<pre>". json_encode($private_key, JSON_PRETTY_PRINT). "</pre>";
echo "<br> <br>";

$time = time(); // The current time
/* membuat signature token */
$jws = Build::jws() // We build a JWS
    ->exp($time + 3600) // The "exp" claim
    ->iat($time) // The "iat" claim
    ->nbf($time) // The "nbf" claim
    ->jti('0123456789', true) // The "jti" claim.
    // The second argument indicate this pair shall be duplicated in the header
    ->alg('HS512') // The signature algorithm. A string or an algorithm class.
    ->iss('issuer') // The "iss" claim
    ->aud('audience1') // Add an audience ("aud" claim)
    ->aud('audience2') // Add another audience
    ->aud('audience3') // Add another audience
    ->sub('subject') // The "sub" claim
    ->claim('https://example.com/isRoot', 'test') // Custom claims/data lain
    ->header('prefs', ['field1', 'field7'])
    ->sign($private_key) // Compute the token with the given JWK
;
echo "<h2>JWS Token</h2>";
echo $jws; // The variable $jws now contains your token
echo "<br> <br>";

$public_key = $private_key->toPublic(); //get public key
echo "<h2>Public Key</h2>";
echo "<pre>".json_encode($public_key, JSON_PRETTY_PRINT). "</pre>";
echo "<br> <br>";

/* memverivikasi signature token dan membaca isi token */
$jwt = Load::jws($jws) // We want to load and verify the token in the variable $token
    ->algs('HS512') // The algorithms allowed to be used
    ->exp() // We check the "exp" claim
    ->iat(1000) // We check the "iat" claim. Leeway is 1000ms (1s)
    ->nbf() // We check the "nbf" claim
    ->aud('audience1') // Allowed audience
    ->iss('issuer') // Allowed issuer
    ->sub('subject') // Allowed subject
    ->jti('0123456789') // Token ID
    ->key($public_key) // Key used to verify the signature
    ->run() // Go!
;

echo "<h2>JWS header</h2>";
echo "<pre>" . json_encode($jwt->header->all(), JSON_PRETTY_PRINT) . "</pre>";
echo "<br> <br>";
echo "<h2>JWS claims</h2>";
echo "<pre>" . json_encode($jwt->claims->all(), JSON_PRETTY_PRINT) . "</pre>";
echo "<br> <br>";
echo "<br> <br>";
echo "<br> <br>";


echo "<h1>JSON Web Encryption (JWE)</h1>";
/* Pembangkit Kunci Enkripsi   */
$private_key = JWKFactory::createRSAKey(
    4096, // Size in bits of the key. We recommend at least 2048 bits.
    [
        'alg' => 'RSA-OAEP-256', // This key must only be used with the RSA-OAEP-256 algorithm
        'use' => 'enc'    // This key is used for encryption/decryption operations only
    ]
);
$public_key = $private_key->toPublic();
$time = time(); // The current time

echo "<h2>Public Key</h2>";
echo "<pre>" . json_encode($public_key, JSON_PRETTY_PRINT) . "</pre>";
echo "<br> <br>";

/* membuat token enkripsi */
$jwe = Build::jwe() // We build a JWE
    ->exp($time + 3600)
    ->iat($time)
    ->nbf($time)
    ->jti(
        '0123456789',
        true
    )
    ->iss('issuer')
    ->aud('audience1')
    ->aud('audience2')
    ->sub('subject')
    ->alg('RSA-OAEP-256') // The "alg" header parameter corresponds to the key encryption algorithm
    ->enc('A256GCM')      // The "enc" header parameter corresponds to the content encryption algorithm
    ->zip('DEF')          // We compress the payload (optional. Only recommended for large set of data)
    ->claim(
        'is_root',
        true
    ) // Custom claims
    ->claim('roles', ['ROLE1' => true, 'ROLE2' => 0.007])
    ->crit(['alg', 'enc']) // We mark some header parameters as critical
    ->encrypt($public_key) // Compute the token with the given JWK (public key)
;
echo "<h2>JWE Token</h2>";
echo $jwe;
echo "<br> <br>";

echo "<h2>Private Key</h2>";
echo "<pre>" . json_encode($private_key, JSON_PRETTY_PRINT) . "</pre>";
echo "<br> <br>";
/* mendekripsi token dan membaca isi token */
$jwt = Load::jwe($jwe) // We want to load and decrypt the token in the variable $token
    ->algs(['RSA-OAEP-256', 'RSA-OAEP']) // The key encryption algorithms allowed to be used
    ->encs(['A256GCM']) // The content encryption algorithms allowed to be used
    ->exp()
    ->iat()
    ->nbf()
    ->aud('audience1')
    ->iss('issuer')
    ->sub('subject')
    ->jti('0123456789')
    ->key($private_key) // Key used to decrypt the token
    ->run() // Go!
;
echo "<h2>JWE header</h2>";
echo "<pre>" . json_encode($jwt->header->all(), JSON_PRETTY_PRINT) . "</pre>";
echo "<br> <br>";
echo "<h2>JWE claims</h2>";
echo "<pre>" . json_encode($jwt->claims->all(), JSON_PRETTY_PRINT) . "</pre>";
