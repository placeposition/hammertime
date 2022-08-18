<?php



/**
 * Cryptor class
 *
 * The encrypt/decrypt methods are largely taken from here:
 * @link https://stackoverflow.com/a/46872528/2532203
 */
class Cryptor {

  /**
   * Holds the encryption algorithm to use
   */
  const ENCRYPTION_ALGORITHM = 'AES-256-CBC';

  /**
   * Holds the hash algorithm to use
   */
  const HASHING_ALGORITHM = 'sha256';

  /**
   * Holds the application encryption secret
   *
   * @var string
   */
  protected $secret;

  /**
   * Cryptor constructor
   *
   * @param string $secret application encryption secret
   */
  public function __construct( string $secret ) {
    $this->secret = $secret;
  }

  /**
   * Decrypts a string using the application secret.
   *
   * @param string $input hex representation of the cipher text
   *
   * @return string UTF-8 string containing the plain text input
   */
  public function decrypt( string $input ): string {

    // we'll need the binary cipher
    $binaryInput = hex2bin( $input );
    $iv             = substr( $binaryInput, 0, 16 );
    $hash           = substr( $binaryInput, 16, 32 );
    $cipherText     = substr( $binaryInput, 48 );
    $key            = hash( Cryptor::HASHING_ALGORITHM, $this->secret, true );

    // if the HMAC hash doesn't match the hash string, something has gone wrong
    if ( hash_hmac( Cryptor::HASHING_ALGORITHM, $cipherText, $key, true ) !== $hash ) {
      return '';
    }

    return openssl_decrypt(
        $cipherText,
        Cryptor::ENCRYPTION_ALGORITHM,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
  }

  /**
   * Encrypts a string using the application secret. This returns a hex representation of the binary cipher text
   *
   * @param string $input plain text input to encrypt
   *
   * @return string hex representation of the binary cipher text
   */
  public function encrypt( string $input ): string {
    $key = hash( Cryptor::HASHING_ALGORITHM, $this->secret, true );
    $iv  = openssl_random_pseudo_bytes( 16 );

    $cipherText = openssl_encrypt(
        $input,
        Cryptor::ENCRYPTION_ALGORITHM,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    $hash       = hash_hmac( Cryptor::HASHING_ALGORITHM, $cipherText, $key, true );

    return bin2hex( $iv . $hash . $cipherText );
  }
}
