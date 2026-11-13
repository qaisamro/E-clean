<?php

namespace App\Support;

class FirebaseCredentials
{
    /**
     * Ordered credential locations. The storage path is preferred over the
     * legacy public path, which is kept only as a transition fallback.
     */
    public static function paths(): array
    {
        return array_values(array_filter([
            env('FIREBASE_CREDENTIALS'),
            env('GOOGLE_APPLICATION_CREDENTIALS'),
            self::storagePath(),
            storage_path('app/firebase/credentials.json'),
            self::publicPath(),
        ], fn ($path) => is_string($path) && trim($path) !== ''));
    }

    public static function storagePath(): string
    {
        return storage_path('app/private/firebase/credentials.json');
    }

    public static function publicPath(): string
    {
        return public_path('firebase_credentials.json');
    }

    public static function exists(): bool
    {
        foreach (self::paths() as $path) {
            if (is_file($path) && is_readable($path)) {
                return true;
            }
        }

        return false;
    }

    public static function publicFileExists(): bool
    {
        return is_file(self::publicPath()) && is_readable(self::publicPath());
    }
}
