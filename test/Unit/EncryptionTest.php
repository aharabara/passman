<?php


use Passman\Encryptor;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase
{

    /**
     * @dataProvider contentAndKeyProvider
     */
    public function testEncryptionWorks(string $content, string $key)
    {
        $encryptor = new Encryptor($key);
        $encryptedValue = $encryptor->encrypt($content);
        $decryptedValue = $encryptor->decrypt($encryptedValue);
        self::assertEquals($content, $decryptedValue, "Decrypted value does not match to the original");
    }

    /**
     * @dataProvider contentAndKeyProvider
     */
    public function testEncryptionFailsBecauseOfTheWrongKey(string $content, string $key)
    {
        $encryptedValue = (new Encryptor($key))->encrypt($content);
        $decryptedValue = (new Encryptor($key . "-wrong-part"))->decrypt($encryptedValue);
        self::assertNotEquals($content, $decryptedValue, "Decrypted value should not match to the original");
    }

    public function contentAndKeyProvider(): array
    {
        return [
            ["test-content", "test-key"],
            ["Lorem ipsum dolor sit amet, consectetur adipisicing elit.", "encrypt"],
            ["Beatae facere in ullam?", "enigma"],
        ];
    }


}