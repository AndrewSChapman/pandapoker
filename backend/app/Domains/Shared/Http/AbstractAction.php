<?php

namespace App\Domains\Shared\Http;

use Laravel\Lumen\Routing\Controller as BaseAction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class AbstractAction extends BaseAction
{
    /** @var array */
    private $responseData = [];

    /** @var Request */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Add a response item to the response array
     * @param string $key The response item key
     * @param mixed $value The response item value
     */
    protected function addToResponse(string $key, $value): void
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key must not be empty');
        }

        $this->responseData[$key] = $value;
    }

    protected function setResponseData(array $data): void
    {
        $this->responseData = $data;
    }

    protected function getResponse(int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($this->responseData, $responseCode);
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }
}
