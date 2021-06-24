<?php

use Passman\EncryptedLoader;
use Passman\Encryptor;
use Passman\Filesystem;
use PHPUnit\Framework\TestCase;

class LoadingAndSavingTest extends TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testGetPasswords(string $pathToPasswords, string $encryptionKey, array $content)
    {

        $encryptor = new Encryptor($encryptionKey);
        $encryptedContent = $encryptor->encrypt(json_encode($content));

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('get')
            ->with($pathToPasswords)
            ->willReturn($encryptedContent);

        $loader = new EncryptedLoader(
            new Encryptor($encryptionKey),
            $filesystem ?? new Filesystem,
            $pathToPasswords
        );
        $passwords = $loader->load();

        self::assertEquals($content, $passwords);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetPasswordsFails(string $pathToPasswords, string $encryptionKey, array $content)
    {

        $encryptor = new Encryptor($encryptionKey . "wrong-part");
        $encryptedContent = $encryptor->encrypt(json_encode($content));

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('get')
            ->with($pathToPasswords)
            ->willReturn($encryptedContent);

        self::expectException(LogicException::class);
        self::expectExceptionMessage("Wrong password.");

        $loader = new EncryptedLoader(
            new Encryptor($encryptionKey),
            $filesystem ?? new Filesystem,
            $pathToPasswords
        );
        $loader->load();
    }


    /**
     * @dataProvider dataProvider
     */
    public function testGetPasswordsWIthEmptyFile(string $pathToPasswords, string $encryptionKey, array $content)
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('get')
            ->with($pathToPasswords)
            ->willReturn("");

        $loader = new EncryptedLoader(new Encryptor($encryptionKey), $filesystem, $pathToPasswords);
        $passwords = $loader->load();
        self::assertEmpty($passwords);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSavePassword(string $pathToPasswords, string $encryptionKey, array $content)
    {

        $encryptor = new Encryptor($encryptionKey);
        $encryptedContent = $encryptor->encrypt(json_encode($content));

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->method('put')
            ->with($pathToPasswords, $encryptedContent)
            ->willReturnCallback(function (string $path, string $content) use ($encryptedContent) {
                self::assertEquals($content, $encryptedContent);
            });

        $loader = new EncryptedLoader(new Encryptor($encryptionKey), $filesystem, $pathToPasswords);
        $loader->save($content);

    }

    public function dataProvider(): array
    {
        return [
            ["filepath", "enigma", ['passman' => "works"]]
        ];
    }

}