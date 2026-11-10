<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    public function isConfigured(): bool
    {
        return filled(config('services.supabase.url'))
            && filled(config('services.supabase.secret_key'))
            && filled(config('services.supabase.storage_bucket'));
    }

    public function upload(UploadedFile $photo, int $studentId): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $objectPath = 'student-profiles/'.$studentId.'/'.Str::uuid().'.'.$photo->extension();
        $response = null;

        try {
            $response = $this->authorizedRequest()
                ->withHeaders([
                    'Content-Type' => $photo->getMimeType() ?: 'application/octet-stream',
                    'x-upsert' => 'false',
                    'cache-control' => '3600',
                ])
                ->withBody($photo->getContent(), $photo->getMimeType() ?: 'application/octet-stream')
                ->post($this->objectUrl($objectPath));
        } catch (ConnectionException) {
            Log::warning('Supabase profile photo upload could not connect.');

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Supabase profile photo upload failed.', [
                'status' => $response->status(),
            ]);

            return null;
        }

        return 'supabase/'.$objectPath;
    }

    public function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, 'supabase/')) {
            if (! $this->isConfigured()) {
                return null;
            }

            $objectPath = Str::after($path, 'supabase/');
            $bucket = rawurlencode(config('services.supabase.storage_bucket'));

            return rtrim(config('services.supabase.url'), '/')
                .'/storage/v1/object/public/'.$bucket.'/'.$this->encodedPath($objectPath);
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.$path)
            : null;
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (! Str::startsWith($path, 'supabase/')) {
            Storage::disk('public')->delete($path);

            return;
        }

        if (! $this->isConfigured()) {
            return;
        }

        $objectPath = Str::after($path, 'supabase/');

        try {
            $response = $this->authorizedRequest()->delete(
                rtrim(config('services.supabase.url'), '/').'/storage/v1/object/'
                    .rawurlencode(config('services.supabase.storage_bucket')),
                ['prefixes' => [$objectPath]],
            );

            if (! $response->successful()) {
                Log::warning('Supabase profile photo deletion failed.', [
                    'status' => $response->status(),
                ]);
            }
        } catch (ConnectionException) {
            Log::warning('Supabase profile photo deletion could not connect.');
        }
    }

    private function authorizedRequest(): PendingRequest
    {
        $secretKey = config('services.supabase.secret_key');
        $headers = [
            'apikey' => $secretKey,
        ];

        if (! Str::startsWith($secretKey, 'sb_secret_')) {
            $headers['Authorization'] = 'Bearer '.$secretKey;
        }

        return Http::timeout(30)->withHeaders($headers);
    }

    private function objectUrl(string $objectPath): string
    {
        return rtrim(config('services.supabase.url'), '/').'/storage/v1/object/'
            .rawurlencode(config('services.supabase.storage_bucket')).'/'
            .$this->encodedPath($objectPath);
    }

    private function encodedPath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }
}
