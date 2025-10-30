<?php

namespace AttReal\Http;

class SuccessJsonResponse extends JsonResponse
{
    public function __construct($data = [], $message = 'Success', $statusCode = 200) {
        # TODO: #1
        # // УСПЕШНЫЙ ОТВЕТ - LARAVEL
        # {
        #     "success": true,
        #     "data": {
        #         "id": 1,
        #         "name": "John",
        #         "email": "john@example.com"
        #     },
        #     "message": "User retrieved successfully",
        #     "meta": {
        #         "timestamp": "2023-01-01T00:00:00Z",
        #         "version": "1.0"
        #     }
        # }

        # TODO: #2 - прийти к этому
        # // УСПЕХ
        # {
        #     "status": "success",
        #     "data": {
        #         "user": {
        #             "id": 1,
        #             "name": "John",
        #             "email": "john@example.com"
        #         }
        #     },
        #     "message": "User created successfully",
        #     "meta": {
        #         "timestamp": "2023-01-01T00:00:00Z",
        #         "version": "1.0",
        #         "pagination": {
        #             "page": 1,
        #             "per_page": 10,
        #             "total": 100
        #         }
        #     }
        # }

        parent::__construct([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }
}
