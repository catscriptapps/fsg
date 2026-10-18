<?php
// /src/Service/VideoUploadService.php

declare(strict_types=1);

namespace Src\Service;

use RuntimeException;

/**
 * Chunked video uploads (the Live Feed composer's "Video" button — the
 * browser sends the file in pieces through modals/video-upload-modal.js).
 * Chunks are appended to a temp file named from the upload's id; the last
 * one is checked (size, real MIME type via finfo) and renamed.
 *
 * The saved file's extension comes from its detected MIME type, never from
 * the uploader's file name, and the upload id may only hold letters, digits
 * and dashes — so nothing can be written as, say, a .php file or outside the
 * upload folder. Requires PHP's fileinfo extension.
 */
class VideoUploadService
{
    private const EXTENSIONS = [
        'video/mp4'       => 'mp4',
        'video/webm'      => 'webm',
        'video/ogg'       => 'ogv',
        'video/quicktime' => 'mov',
    ];

    private string $uploadDir;
    private int $maxSize; // bytes

    public function __construct(string $uploadDir, int $maxSizeMb = 200)
    {
        $this->uploadDir = rtrim($uploadDir, '/\\') . '/';
        $this->maxSize = $maxSizeMb * 1024 * 1024;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * @return array{status: string, fileName?: string}
     */
    public function handleChunk(array $file, string $uuid, int $index, int $total): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->errorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
        }
        if (!preg_match('/^[A-Za-z0-9-]{8,64}$/', $uuid)) {
            throw new RuntimeException('Invalid upload id.');
        }
        if ($total < 1 || $index < 0 || $index >= $total) {
            throw new RuntimeException('Invalid chunk.');
        }

        $tempPath = $this->uploadDir . 'temp_' . $uuid . '.part';
        if ($index === 0 && is_file($tempPath)) {
            unlink($tempPath); // a retried upload starts over
        }

        $chunk = file_get_contents($file['tmp_name']);
        if ($chunk === false || file_put_contents($tempPath, $chunk, FILE_APPEND) === false) {
            throw new RuntimeException('Could not save part of the video.');
        }
        if (filesize($tempPath) > $this->maxSize) {
            unlink($tempPath);
            throw new RuntimeException('Videos can be up to ' . ($this->maxSize / 1024 / 1024) . ' MB.');
        }

        return $index + 1 === $total ? $this->finalize($tempPath) : ['status' => 'uploading'];
    }

    private function finalize(string $tempPath): array
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tempPath);
        $ext = self::EXTENSIONS[$mime] ?? null;
        if ($ext === null) {
            unlink($tempPath);
            throw new RuntimeException('That file is not a supported video (MP4, MOV, WEBM or OGG).');
        }

        $fileName = 'vid_' . bin2hex(random_bytes(10)) . '.' . $ext;
        if (!rename($tempPath, $this->uploadDir . $fileName)) {
            throw new RuntimeException('Could not finish saving the video.');
        }

        return ['status' => 'completed', 'fileName' => $fileName];
    }

    private function errorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Part of the video was larger than the server allows.',
            UPLOAD_ERR_PARTIAL => 'Part of the video only arrived partly — please try again.',
            UPLOAD_ERR_NO_FILE => 'No video was received.',
            default => 'The video could not be uploaded.',
        };
    }
}
