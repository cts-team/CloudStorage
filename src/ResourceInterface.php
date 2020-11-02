<?php


namespace CoverCMS\CloudStorage;


interface ResourceInterface
{
    public function upload(string $key, string $content, array $options = []);

    public function initMultipartUpload(string $key, array $options = []): string;

    public function uploadPart(string $key, string $content, int $partNumber, string $uploadId);

    public function completeMultipartUpload(string $key, string $uploadId, array $parts);

    public function remove($key, array $options = []);

    public function isExist(string $key, array $options = []): bool;

    public function meta(string $key, array $options = []);
}