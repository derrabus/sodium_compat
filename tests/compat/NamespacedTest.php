<?php

/**
 * Class SodiumCompatTest
 */
class NamespacedTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (PHP_VERSION_ID < 50300) {
            $this->markTestSkipped('PHP < 5.3.0; skipping PHP 5.3+ compatibility test suite.');
        }
        ParagonIE_Sodium_Compat::$disableFallbackForUnitTests = true;
    }

    /**
     * @covers ParagonIE_Sodium_Compat::crypto_secretbox()
     */
    public function testCryptoSecretBox()
    {
        $key = str_repeat("\x80", 32);
        $nonce = str_repeat("\x00", 24);
        $message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
        $boxfunc = '\\ParagonIE\\Sodium\\Compat::crypto_secretbox';
        $openfunc = '\\ParagonIE\\Sodium\\Compat::crypto_secretbox_open';

        $this->assertSame(
            ParagonIE_Sodium_Core_Util::substr(
                bin2hex($boxfunc($message, $nonce, $key)),
                0, 32
            ),
            ParagonIE_Sodium_Core_Util::substr(
                bin2hex(ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key)),
                0, 32
            ),
            'secretbox - short messages'
        );
        $this->assertSame(
            $message,
            ParagonIE_Sodium_Crypto::secretbox_open(
                $boxfunc($message, $nonce, $key),
                $nonce,
                $key
            )
        );
        $this->assertSame(
            $message,
            $openfunc(
                ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key),
                $nonce,
                $key
            )
        );
        $message = str_repeat('a', 97);
        $this->assertSame(
            bin2hex($boxfunc($message, $nonce, $key)),
            bin2hex(ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key)),
            'secretbox - long messages (multiple of 16)'
        );

        $message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";

        $message = str_repeat($message, 16);

        $this->assertSame(
            bin2hex($boxfunc($message, $nonce, $key)),
            bin2hex(ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key)),
            'secretbox - long messages (multiple of 16)'
        );

        $message .= 'a';

        $this->assertSame(
            bin2hex($boxfunc($message, $nonce, $key)),
            bin2hex(ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key)),
            'secretbox - long messages (NOT a multiple of 16)'
        );

        $message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";

        $this->assertSame(
            bin2hex($boxfunc($message, $nonce, $key)),
            bin2hex(ParagonIE_Sodium_Crypto::secretbox($message, $nonce, $key)),
            'secretbox - medium messages'
        );
    }
}