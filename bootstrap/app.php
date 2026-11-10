<?php

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Mengecualikan endpoint SOAP dari pemeriksaan CSRF.
        $middleware->validateCsrfTokens(except: [
            'soap/*',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Memastikan seluruh error pada endpoint API dirender sebagai JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) =>
                $request->is('api/*') || $request->expectsJson()
        );

        // 422 - Validasi gagal.
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'type' => url('/docs/errors/validation-failed'),
                'title' => 'Unprocessable Content',
                'status' => 422,
                'detail' => 'Satu atau lebih atribut input tidak memenuhi aturan validasi.',
                'instance' => $request->getRequestUri(),
                'errors' => $e->errors(),
            ], 422)->header(
                'Content-Type',
                'application/problem+json'
            );
        });

        // 401 - Belum login atau token tidak valid.
        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'type' => url('/docs/errors/unauthenticated'),
                'title' => 'Unauthorized',
                'status' => 401,
                'detail' => 'Akses ditolak. Sertakan Bearer Token yang valid.',
                'instance' => $request->getRequestUri(),
            ], 401)->header(
                'Content-Type',
                'application/problem+json'
            );
        });

        // 404 - Endpoint atau data tidak ditemukan.
        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'type' => url('/docs/errors/not-found'),
                'title' => 'Not Found',
                'status' => 404,
                'detail' => 'Endpoint atau data yang diminta tidak ditemukan.',
                'instance' => $request->getRequestUri(),
            ], 404)->header(
                'Content-Type',
                'application/problem+json'
            );
        });

        // Memastikan response 422 API menggunakan format RFC 7807.
        $exceptions->respond(function (Response $response) {

            $request = request();

            if (
                $request->is('api/*') &&
                $response->getStatusCode() === 422
            ) {
                $data = json_decode($response->getContent(), true);

                return response()->json([
                    'type' => url('/docs/errors/validation-failed'),
                    'title' => 'Unprocessable Content',
                    'status' => 422,
                    'detail' => 'Satu atau lebih atribut input tidak memenuhi aturan validasi.',
                    'instance' => $request->getRequestUri(),
                    'errors' => $data['errors'] ?? [],
                ], 422)->header(
                    'Content-Type',
                    'application/problem+json'
                );
            }

            return $response;
        });

    })->create();
