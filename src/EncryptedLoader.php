<?php

namespace Passman;

use LogicException;

class EncryptedLoader
{
    private Encryptor $encryptor;
    private Filesystem $filesystem;
    private string $filePath;

    public function __construct(Encryptor $encryptor, Filesystem $filesystem, string $filePath)
    {
        $this->encryptor = $encryptor;
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
    }


    public function load(): array
    {
        $content = $this
            ->filesystem
            ->get($this->filePath);

        if (empty($content)) {
            return [];
        }
        $passwords = json_decode($this->encryptor->decrypt($content), true);
        if ($passwords === null) {
            throw new LogicException("Wrong password.");
        }
        return $passwords;
    }


    public function save(array $data): void
    {
        $encryptedContent = $this
            ->encryptor
            ->encrypt(json_encode($data));

        $this
            ->filesystem
            ->put($this->filePath, $encryptedContent);
    }

    public function getPath(): string
    {
        return $this->filePath;
    }
}