# Cara Penggunaan

## 1. install composer dan jalankan `composer require web-token/jwt-framework` pada direktori yang di inginkan menggunakan Command Prompt/Terminal

**contoh**

```batchfile
C:\htdocs\plugins\JWT-Framework> composer require web-token/jwt-framework
```

## 2. gunakan `require_once('JWT-Framework/vendor/autoload.php');` untuk memanggil library yang sudah diatur menggunakan `namespace`

## 3. gunakan `use Jose\Component\KeyManagement\JWKFactory;` untuk membangkitkan kunci

### **Octet String**

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

### **RSA Key Pair**

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

### **Elliptic Curve Key Pair**

```php
$private_key = JWKFactory::createECKey('P-256');
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

### **Octet Key Pair**

```php
$private_key = JWKFactory::createOKPKey('X25519');
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

### **None Key**

```php
$private_key = JWKFactory::createNoneKey();
```

### **Create Key From External Sources**
#### **From Values**
```php
$private_key = JWKFactory::createFromValues([
    'kid' => '71ee230371d19630bc17fb90ccf20ae632ad8cf8',
    'kty' => 'RSA',
    'alg' => 'RS256',
    'use' => 'sig',
    'n' => 'vnMTRCMvsS04M1yaKR112aB8RxOkWHFixZO68wCRlVLxK4ugckXVD_Ebcq-kms1T2XpoWntVfBuX40r2GvcD9UsTFt_MZlgd1xyGwGV6U_tfQUll5mKxCPjr60h83LXKJ_zmLXIqkV8tAoIg78a5VRWoms_0Bn09DKT3-RBWFjk=',
    'e' => 'AQAB',
]);
```
#### **From A Key File**
```php
$private_key = JWKFactory::createFromKeyFile(
    '/path/to/my/key/file.pem', // The filename
    'Secret',                   // Secret if the key is encrypted
    [
        'use' => 'sig',         // Additional parameters
    ]
);
```
#### **From A PKCS#12 Certificate**
```php
$private_key = JWKFactory::createFromPKCS12CertificateFile(
    '/path/to/my/key/file.p12', // The filename
    'Secret',                   // Secret if the key is encrypted
    [
        'use' => 'sig',         // Additional parameters
    ]
);
```
#### **From A PKCS#12 Certificate**
```php
$private_key = JWKFactory::createFromCertificateFile(
    '/path/to/my/key/file.crt', // The filename
    [
        'use' => 'sig',         // Additional parameters
    ]
);
```

### **Untuk mendapatkan public key**
```php
$public_key = $private_key->toPublic();
```

## 4. Gunakan `use Jose\Easy\Build;` untuk membangkitkan token dengan menggukakan key yang telah dibuat sebelumnya

### **Membangkitkan token JWS**

JWS dibangkitkan dengan menggunakan private key

```php
$token = Build::jws() // We build a JWS
    ->exp($time + 3600) // The "exp" claim
    ->iat($time) // The "iat" claim
    ->nbf($time) // The "nbf" claim
    ->jti('0123456789', true) // The "jti" claim.
                              // The second argument indicate this pair shall be duplicated in the header
    ->alg('RS512') // The signature algorithm. A string or an algorithm class.
    ->iss('issuer') // The "iss" claim
    ->aud('audience1') // Add an audience ("aud" claim)
    ->aud('audience2') // Add another audience
    ->sub('subject') // The "sub" claim
    ->claim('https://example.com/isRoot', true)
    ->header('prefs', ['field1', 'field7'])
    ->sign($private_key) // Compute the token with the given JWK
;
```

### **Membangkitkan token JWE**
JWE dibangkitkan dengan menggunakan public key
```php
$token = Build::jwe() // We build a JWE
    ->exp($time + 3600)
    ->iat($time)
    ->nbf($time)
    ->jti('0123456789', true)
    ->iss('issuer')
    ->aud('audience1')
    ->aud('audience2')
    ->sub('subject')
    ->alg('RSA-OAEP-256') // The "alg" header parameter corresponds to the key encryption algorithm
    ->enc('A256GCM')      // The "enc" header parameter corresponds to the content encryption algorithm
    ->zip('DEF')          // We compress the payload (optional. Only recommended for large set of data)
    ->claim('is_root', true) // Custom claims
    ->claim('roles', ['ROLE1' => true, 'ROLE2' => 0.007])
    ->crit(['alg', 'enc']) // We mark some header parameters as critical
    ->encrypt($public_key) // Compute the token with the given JWK (public key)
;
```
## 5. Gunakan `use Jose\Easy\Load;` untuk proses verifikasi token dan membuka isi/mendekripsi token.

## **Memverifikasi dan membuka isi token JWS**
memverifikasi dan membuka isi token JWS dengan menggunakan public key

```php
$jwt = Load::jws($token) // We want to load and verify the token in the variable $token
    ->algs(['RS256', 'RS512']) // The algorithms allowed to be used
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
```
### **Memverifikasi dan Mendekripsi token JWE**
memverifikasi dan mendekripsi token JWE menggunakan private key

```php
$jwt = Load::jwe($token) // We want to load and decrypt the token in the variable $token
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
```

# Referensi 

[https://web-token.spomky-labs.com/the-easy-way/easy](https://web-token.spomky-labs.com/the-easy-way/easy)

[https://web-token.spomky-labs.com/the-components/encrypted-tokens-jwe/encryption-algorithms](https://web-token.spomky-labs.com/the-components/encrypted-tokens-jwe/encryption-algorithms)

[https://web-token.spomky-labs.com/the-components/signed-tokens-jws/signature-algorithms](https://web-token.spomky-labs.com/the-components/signed-tokens-jws/signature-algorithms)

[https://web-token.spomky-labs.com/the-components/key-jwk-and-key-set-jwkset/key-management](https://web-token.spomky-labs.com/the-components/key-jwk-and-key-set-jwkset/key-management)

