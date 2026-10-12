<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (SymfonyResponse)  $next
     * @param  string  $mode  'optional' or 'required'
     */
    public function handle(Request $request, Closure $next, string $mode = 'optional'): SymfonyResponse
    {
        // Only mutate methods should be idempotent (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $idempotencyKey = $this->extractIdempotencyKey($request);

        if (! $idempotencyKey) {
            if ($mode === 'required') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Header X-Idempotency-Key wajib disertakan untuk permintaan ini.',
                ], 400);
            }

            return $next($request);
        }

        // Validate key length
        if (strlen($idempotencyKey) < 4 || strlen($idempotencyKey) > 255) {
            return response()->json([
                'status' => 'error',
                'message' => 'Panjang X-Idempotency-Key tidak valid (harus antara 4 dan 255 karakter).',
            ], 422);
        }

        $userId = $request->user()?->id ?? 'guest:'.$request->ip();
        $routeSignature = md5($request->method().':'.$request->path());
        $cacheKey = "idempotency:{$userId}:{$routeSignature}:{$idempotencyKey}";
        $lockKey = "lock:{$cacheKey}";

        $payloadChecksum = $this->calculatePayloadChecksum($request);

        // Check if an existing cached response exists
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $this->handleCachedEntry($cached, $payloadChecksum);
        }

        // Acquire atomic lock to prevent concurrent execution of the same key
        $lock = null;
        try {
            $lock = Cache::lock($lockKey, 15);
            $lock->block(5);
        } catch (LockTimeoutException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan dengan kunci ini sedang diproses. Mohon tunggu sejenak.',
            ], 409);
        } catch (\BadMethodCallException $e) {
            // Cache driver does not support locking (fallback gracefully)
            $lock = null;
        }

        try {
            // Re-check cache in case concurrent request completed while waiting for lock
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $this->handleCachedEntry($cached, $payloadChecksum);
            }

            // Mark request as in-progress
            Cache::put($cacheKey, [
                'status' => 'in_progress',
                'checksum' => $payloadChecksum,
            ], 60);

            $response = $next($request);

            // Only cache successful or redirection responses (200 - 399)
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 400) {
                $this->cacheResponse($cacheKey, $payloadChecksum, $response);
            } else {
                // Clear in-progress marker so client can correct client error and retry
                Cache::forget($cacheKey);
            }

            return $response;
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
            throw $e;
        } finally {
            if ($lock) {
                try {
                    $lock->release();
                } catch (\Throwable $e) {
                    // Ignore release errors if lock expired
                }
            }
        }
    }

    /**
     * Extract idempotency key from header or body.
     */
    protected function extractIdempotencyKey(Request $request): ?string
    {
        $key = $request->header('X-Idempotency-Key')
            ?? $request->header('Idempotency-Key')
            ?? $request->input('_idempotency_key')
            ?? $request->input('idempotency_key');

        return is_string($key) && trim($key) !== '' ? trim($key) : null;
    }

    /**
     * Calculate SHA-256 checksum of request parameters.
     */
    protected function calculatePayloadChecksum(Request $request): string
    {
        $data = $request->except(['_token', '_idempotency_key', 'idempotency_key']);

        return hash('sha256', json_encode($data));
    }

    /**
     * Handle cached entry resolution.
     */
    protected function handleCachedEntry(array $cached, string $payloadChecksum): SymfonyResponse
    {
        if ($cached['checksum'] !== $payloadChecksum) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kunci idempotensi telah digunakan untuk permintaan dengan data berbeda (Idempotency conflict).',
            ], 409);
        }

        if ($cached['status'] === 'in_progress') {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan dengan kunci ini sedang diproses. Mohon tunggu sejenak.',
            ], 409);
        }

        // Replay cached response
        $headers = $cached['headers'] ?? [];
        $headers['X-Idempotent-Replay'] = 'true';

        return response($cached['content'], $cached['code'], $headers);
    }

    /**
     * Store response in cache for 24 hours.
     */
    protected function cacheResponse(string $cacheKey, string $payloadChecksum, SymfonyResponse $response): void
    {
        $filteredHeaders = [];
        foreach ($response->headers->all() as $name => $values) {
            if (! in_array(strtolower($name), ['set-cookie', 'date', 'x-request-id'])) {
                $filteredHeaders[$name] = $values;
            }
        }

        Cache::put($cacheKey, [
            'status' => 'completed',
            'checksum' => $payloadChecksum,
            'code' => $response->getStatusCode(),
            'headers' => $filteredHeaders,
            'content' => $response->getContent(),
        ], now()->addHours(24));
    }
}
