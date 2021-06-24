<?php

namespace Passman;

use LogicException;

class EncryptedLoader
{
    private Encryptor $encryptor;
    private Filesystem $filesystem;

    public function __construct(Encryptor $encryptor, Filesystem $filesystem)
    {
        $this->encryptor = $encryptor;
        $this->filesystem = $filesystem;
    }


    public function load(string $filePath): array
    {
        $content = $this
            ->filesystem
            ->get($filePath);

        if (empty($content)) {
            return [];
        }
        $passwords = json_decode($this->encryptor->decrypt($content), true);
        if ($passwords === null) {
            throw new LogicException("Wrong password.");
        }
        return $passwords;
    }


    public function save(string $filePath, array $data): void
    {
        $encryptedContent = $this
            ->encryptor
            ->encrypt(json_encode($data));

        $this
            ->filesystem
            ->put($filePath, $encryptedContent);
    }

}