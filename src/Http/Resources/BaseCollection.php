<?php
namespace Mdphp\Region\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        if (isset($jsonResponse['links']) && isset($jsonResponse['meta'])) {
            if (isset($jsonResponse['data'])) {
                $data = $jsonResponse['data'];
            }
            if (isset($jsonResponse['meta']['current_page'])) {
                $data['page'] = $jsonResponse['meta']['current_page'];
            }
            if (isset($jsonResponse['meta']['per_page'])) {
                $data['page_size'] = $jsonResponse['meta']['per_page'];
            }
            if (isset($jsonResponse['meta']['last_page'])) {
                $data['total_page'] = $jsonResponse['meta']['last_page'];
            }
            if (isset($jsonResponse['meta']['total'])) {
                $data['total'] = $jsonResponse['meta']['total'];
            }
            // unset($jsonResponse['links'], $jsonResponse['meta']);
            $jsonResponse = $data;
        }
        $jsonResponse = array_merge($jsonResponse, [
            'error_code' => 0,
            'message' => 'success',
            'errors' => []
        ]);
        $response->setContent(json_encode($jsonResponse));
        // $response->header('Content-Type', 'text/html; charset=UTF-8');
        $response->header('Content-Type', 'application/json; charset=UTF-8');
    }
}