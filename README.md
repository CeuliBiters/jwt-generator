# Cara Penggunaan

1. install composer dan jalankan

```cmd
composer require web-token/jwt-framework
```

pada direktori yang di inginkan

**contoh**

```cmd
C:\htdocs\plugins\JWT-Framework> composer require web-token/jwt-framework
```

2. gunakan `require_once('../JWT-Framework/vendor/autoload.php');` untuk memanggil library yang sudah diatur menggunakan `namespace`

3. gunakan `use Jose\Component\KeyManagement\JWKFactory;` untuk membangkitkan kunci

## **Octet String**

```php
$key = JWKFactory::createOctKey(
1024, // Size in bits of the key. We recommend at least 128 bits.
    [
        'alg' => 'HS256', // This key must only be used with the HS256 algorithm
        'use' => 'sig'    // This key is used for signature\/verification operations only atau 'enc' unutk enskripsi
    ]
);
```
atau penggunaan jika sudah memiliki shared secret
```php
$jwk = JWKFactory::createFromSecret(
    'My Secret Key',       // The shared secret
    [                      // Optional additional members
        'alg' => 'HS256',
        'use' => 'sig'
    ]
);
```

* **algoritma yang digunakan untuk signature**
    * >HS256
    * >HS384
    * >HS512
* **algoritma yang digunakan untuk enkripsi**
    * >A128KW
    * >A192KW
    * >A256KW
    * >A128GCMKW
    * >A192GCMKW
    * >A256GCMKW
    * >dir

## **RSA Key Pair**

```php
$private_key = JWKFactory::createRSAKey(
    4096, // Size in bits of the key. We recommend at least 2048 bits.
    [
        'alg' => 'RSA-OAEP-256', // This key must only be used with the RSA-OAEP-256 algorithm
        'use' => 'enc'    // This key is used for encryption/decryption operations only atau 'enc' unutk enskripsi
    ]);
```

* **algoritma yang digunakan untuk signature**
    * >RS256
    * >RS384
    * >RS512
    * >PS256
    * >PS384
    * >PS512
* **algoritma yang digunakan untuk enkripsi**
    * >RSA1_5
    * >RSA-OAEP
    * >RSA-OAEP-256

## **Elliptic Curve Key Pair**

```php
$key = JWKFactory::createECKey('P-256');
```

* **curves yang di gunakan** 
    * >P-256
    * >P-384
    * >P-521
    * >secp256k1
* **algoritma yang digunakan untuk signature**
    * >ES256
    * >ES384
    * >ES512
    * >ES256K
* **algoritma yang digunakan untuk enkripsi**
    * >ECDH-ES
    * >ECDH-ES+A128KW
    * >ECDH-ES+A192KW
    * >ECDH-ES+A256KW

## **Octet Key Pair**

```php
$key = JWKFactory::createOKPKey('X25519');
```

* **curves yang di gunakan**
    * >Ed25519
    * >X25519
* **algoritma yang digunakan untuk signature**
    * >ES256
    * >ES384
    * >ES512
    * >ES256K
* **algoritma yang digunakan untuk enkripsi**
    * >ECDH-ES
    * >ECDH-ES+A128KW
    * >ECDH-ES+A192KW
    * >ECDH-ES+A256KW

## **None Key**

```php
$key = JWKFactory::createNoneKey();
```