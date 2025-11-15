<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected Client $client;
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.key');
        $this->bucket = config('services.supabase.bucket');

        $this->client = new Client([
            'base_uri' => $this->url . '/storage/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->key,
                'apikey' => $this->key,
            ],
        ]);
    }

    /**
     * Upload file to Supabase Storage
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string|null Returns the file path or null on failure
     */
    public function upload(UploadedFile $file, string $folder = 'avatars'): ?string
    {
        try {
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $folder . '/' . $filename;

            // Upload to Supabase
            $response = $this->client->post("object/{$this->bucket}/{$path}", [
                'headers' => [
                    'Content-Type' => $file->getMimeType(),
                    'x-upsert' => 'false',
                ],
                'body' => fopen($file->getRealPath(), 'r'),
            ]);

            if ($response->getStatusCode() === 200) {
                return $path;
            }

            Log::error('Supabase upload failed', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ]);

            return null;
        } catch (GuzzleException $e) {
            Log::error('Supabase upload exception', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

    /**
     * Delete file from Supabase Storage
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            $response = $this->client->delete("object/{$this->bucket}/{$path}");

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            Log::error('Supabase delete exception', [
                'message' => $e->getMessage(),
                'path' => $path,
            ]);

            return false;
        }
    }

    /**
     * Get public URL for a file
     *
     * @param string $path
     * @return string
     */
    public function getPublicUrl(string $path): string
    {
        return $this->url . '/storage/v1/object/public/' . $this->bucket . '/' . $path;
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        try {
            $response = $this->client->head("object/{$this->bucket}/{$path}");

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
