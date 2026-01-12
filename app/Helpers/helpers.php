<?php

if (!function_exists('hash_encode')) {
    /**
     * Encode numeric ID to hash string
     *
     * @param int $id
     * @return string
     */
    function hash_encode($id)
    {
        return base64_encode($id . config('app.key'));
    }
}

if (!function_exists('hash_decode')) {
    /**
     * Decode hash string back to numeric ID
     *
     * @param string $hash
     * @return int|null
     */
    function hash_decode($hash)
    {
        $decoded = base64_decode($hash);
        $appKey = config('app.key');
        
        if (str_ends_with($decoded, $appKey)) {
            return (int) str_replace($appKey, '', $decoded);
        }
        
        return null;
    }
}
