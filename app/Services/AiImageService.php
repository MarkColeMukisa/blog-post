<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Gemini\Laravel\Facades\Gemini;

class AiImageService
{
    public static function generate(string $title): ?string
    {
        $prompt = "
            Create a clean, modern, high-quality cover image for a blog post titled:
            '{$title}'.

            Rules:
            - No text in the image
            - Professional blog cover style
        ";

        $model = Gemini::generativeModel('gemini-2.0-flash');

        $response = $model->generateContent([
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ]);

        // Extract base64 image from response
        $imageBase64 = $response
            ->candidates[0]
            ->content
            ->parts[0]
            ->inlineData
            ->data;

        $binary = base64_decode($imageBase64);

        $path = 'posts/covers/' . uniqid() . '.png';

        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
