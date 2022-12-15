<?php
namespace Mdphp\Region\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        $jsonResponse = array_merge($jsonResponse, [
            'error_code' => 0,
            'message' => 'success',
            'errors' => [],
        ]);
        if ($authorization = $request->Authorization) {
            $response->header('Authorization', $authorization);
        }
        $response->setContent(json_encode($jsonResponse));
        $response->header('Content-Type', 'application/json; charset=UTF-8');
    }
}