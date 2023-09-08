<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public int $itemsPerPage = 20;
    public string $sort_by = 'updated_at';
    public string $sort_dir = 'desc';

    /**
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public function error(string $message ='', int $status = 400): JsonResponse
    {
        return Response::json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * @param array $params
     * @param int $status
     * @return JsonResponse
     */
    public function success(array $params = [], int $status = 200): JsonResponse
    {
        return Response::json(
            array_merge(
                ['status' => 'success'],
                $params
            ),
            $status
        );
    }
}

