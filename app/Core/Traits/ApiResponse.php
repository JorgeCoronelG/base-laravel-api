<?php

namespace App\Core\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ApiResponse
{
    /**
     * Función que retorna una respuesta JSON exitosa
     */
    protected function successResponse(
        ResourceCollection | JsonResource | array $data,
        int $code
    ): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Función que retorna una respuesta JSON con contenido de algún archivo
     */
    protected function fileResponse(string $pathFile, array $headers = []): BinaryFileResponse
    {
        return response()->file($pathFile, $headers);
    }

    /**
     * Función que retorna una respuesta JSON errones
     */
    protected function errorResponse(array | string $message, int $code): JsonResponse
    {
        return response()->json(['code' => $code, 'error' => $message], $code);
    }

    /**
     * Función que retorna una respuesta 204 no content
     */
    protected function noContentResponse(): Response
    {
        return response()->noContent();
    }

    /**
     * Función que retorna un JSON con un listado de registros
     */
    protected function showAll(ResourceCollection $collection, int $code = 200): JsonResponse
    {
        return $this->successResponse($collection, $code);
    }

    /**
     * Función que retorna un JSON con un registro
     */
    protected function showOne(JsonResource $resource, int $code = 200): JsonResponse
    {
        return $this->successResponse($resource, $code);
    }
}
