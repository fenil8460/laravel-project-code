<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseAPI
{
    /**
     * Core of response
     *
     * @param   string          $message
     * @param   array|object    $data
     * @param   integer         $statusCode
     * @param   boolean         $isSuccess
     */
    // public function coreResponse($message, $data = null, $statusCode, $isSuccess = true)
    // {
    //     // Check the params
    //     if (!$message) {
    //         return response()->json(['message' => 'Message is required'], 500);
    //     }

    //     // Send the response
    //     if ($isSuccess) {
    //         return response()->json([
    //             'success' => $message,
    //             'code' => $statusCode,
    //             'results' => $data
    //         ], $statusCode);
    //     } else {
    //         return response()->json([
    //             'success' => $message,
    //             'error' => true,
    //             'code' => $statusCode,
    //         ], $statusCode);
    //     }
    // }

    // /**
    //  * Send any success response
    //  *
    //  * @param   string          $message
    //  * @param   array|object    $data
    //  * @param   integer         $statusCode
    //  */
    // public function success($message, $data = null, $statusCode = 200)
    // {
    //     return $this->coreResponse($message, $data, $statusCode);
    // }

    // /**
    //  * Send any error response
    //  *
    //  * @param   string          $message
    //  * @param   integer         $statusCode
    //  */
    // public function error($message, $statusCode = 500)
    // {
    //     return $this->apiResponse(
    //         ['success' => false, 'errorMsg' => $message],
    //         $code
    //     );
    //     return $this->coreResponse($message, null, $statusCode, false);
    // }



    private function coreResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    public function success($contents, $is_data = true)
    {
        $data = ['success' => true];
        if ($is_data) {
            $data['results'] = $contents;
        } else {
            $data = array_merge($data, $contents);
        }

        return $this->coreResponse($data);
    }



    public function error($message, $code = 422): JsonResponse
    {
        return $this->coreResponse(
            ['success' => false, 'message' => $message],
            $code
        );
    }


}
