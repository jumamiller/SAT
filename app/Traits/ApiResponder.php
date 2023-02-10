<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponder
{
    /**
     * @param bool $success
     * @param string $message
     * @param int $code
     * @param $data
     * @param $meta
     * @param $errors
     * @return JsonResponse
     */
    protected function success(bool $success,string $message,$data, int $code,$meta=null,$errors=null): JsonResponse
    {
        return response()->json([
            'success'   =>$success,
            'message'   =>$message,
            'data'      =>$data,
            'meta'      =>$meta,
            'errors'    =>$errors
        ], $code);
    }

    /**
     * @param bool $success
     * @param string|null $message
     * @param int $code
     * @param $data
     * @param $meta
     * @param $errors
     * @return JsonResponse
     */
    protected function error(bool $success,string $message = null, int $code = 500,$meta=null,$errors=null): JsonResponse
    {
        return response()->json([
            'success'   =>$success,
            'message'   =>$message,
            'meta'      =>$meta,
            'errors'    =>$errors
        ], $code);
    }
    /**
     * @param $message
     * @param int $code
     * @return JsonResponse
     * @throws \Exception
     */
    protected function exception($message,$code): JsonResponse
    {
        if ($message instanceof \Exception)
            throw $message;

        return $this->error(false,$message, $code);
    }
}
