<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{

    protected $message; // custom message
    protected $status; // false or true
    protected $httpCode; // http code example 400, 401, 200
    protected $statusCode; // one number code big then 1000
    protected $result = [];
    protected $errors = [];

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function setData(array $data = [])
    {
        $this->result['data'] = $data;
        return $this;
    }


    /**
     * @param $message
     * @param bool $status
     * @param array $errors
     */
    private function setMsgAndStatus($message, bool $status, array $errors = [])
    {
        $this->result["status_code"] = $this->statusCode;
        $this->result['errors']      = $errors;
        $this->result["status"]    	 = $status;
        $this->result["message"]     = $message;
    }

    /**
     * @return JsonResponse
     */
    public function response():JsonResponse
    {
        $this->result['data'] = $this->result['data'] ?? [];
        return response()->json($this->result, $this->httpCode, [], JSON_PRESERVE_ZERO_FRACTION);
    }


    /**
     * @param int $httpCode
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public function successResponse(int $httpCode = 200, int $statusCode = 1000, string $message = 'Your request successfully handled.'):JsonResponse
    {
        $this->httpCode = $httpCode;
        $this->statusCode = $statusCode;

        $this->setMsgAndStatus($message, true);

        $data = $this->result['data'];
        unset($this->result['data']);
        $this->result['data'] = $data;

        return $this->response();
    }


    /**
     * @param string $message
     * @return JsonResponse
     */
    public function createdResponse($message = 'Your request successfully handled.'):JsonResponse
    {
        $this->httpCode = 201;
        $this->statusCode = 2001;

        $this->setMsgAndStatus($message, true);

        return $this->response();
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function notFoundResponse($message = 'Your requested resource was not found.'):JsonResponse
    {
        $this->httpCode  = 404;
        $this->statusCode = 4004;

        $this->setMsgAndStatus($message, false);

        return $this->response();

    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function failedResponse($message = 'Sorry. Internal server error.'):JsonResponse
    {
        $this->httpCode    = 500;
        $this->statusCode = 5000;

        $this->setMsgAndStatus($message, false);

        return $this->response();

    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function notAuthenticatedResponse($message = 'Not Authenticated. Make sure your token is valid'):JsonResponse
    {
        $this->httpCode    = 401;
        $this->statusCode  = 4001;

        $this->setMsgAndStatus($message, false);

        return $this->response();

    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function methodNotAllowedHttpException($message = 'Method not allowed!'):JsonResponse
    {
        $this->httpCode    = 405;
        $this->statusCode = 4005;

        $this->setMsgAndStatus($message, false);
        return $this->response();

    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function notAuthorizedResponse($message = 'Your are not allowed!'):JsonResponse
    {
        $this->httpCode    = 403;
        $this->statusCode = 4003;

        $this->setMsgAndStatus($message, false);

        return $this->response();

    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function badRequestResponse($message = 'The request is not in accepted format'):JsonResponse
    {
        $this->httpCode    = 400;
        $this->statusCode = 4000;

        $this->setMsgAndStatus($message, false);
        return $this->response();

    }

    /**
     *
     * @param string $message
     * @param array  $errors
     * @return JsonResponse
     */
    public function validationErrorResponse(array $errors, $message = 'The request is not in accepted format')
    {
        $this->httpCode   = 400;
        $this->statusCode = 4000;

        $this->setMsgAndStatus($message, false, $errors);

        return $this->response();
    }


    /**
     * @param $data
     * @param bool $status
     * @param int $statusCode
     * @param int $httpCode
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function customResponse($data,  bool $status = true, int $statusCode = 1000, int $httpCode = 200, string $message = 'Your request successfully handled.', array $errors = []) : JsonResponse
    {
        $this->httpCode      = $httpCode;
        $this->errors = $errors;
        $this->statusCode    = $statusCode;
        $this->result['data'] = $data;

        $this->setMsgAndStatus($message, $status);

        return $this->response();

    }

    /**
     * @param array $result
     * @return array
     */
    private function getResponseData(array $result) : array
    {
        if(isset($result['data']['message']) &&
            isset($result['data']['status'])  &&
            isset($result['data']['errors'])  &&
            isset($result['data']['status_code']) &&
            isset($result['data']['data'])){

            return $result['data'];

        }else{
            $result['data'] = $result['data'] ?? [];
            return $result ;
        }
    }

}
