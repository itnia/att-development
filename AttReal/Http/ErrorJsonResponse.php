<?php

namespace AttReal\Http;

class ErrorJsonResponse extends JsonResponse
{
    public function __construct($message = 'Error', $errors = [], $statusCode = 400) {
        # TODO: #1
        # // ОШИБКА ВАЛИДАЦИИ
        # {
        #     "success": false,
        #     "error": {
        #         "code": "VALIDATION_ERROR",
        #         "message": "The given data was invalid",
        #         "details": {
        #             "email": ["The email must be a valid email address"],
        #             "password": ["The password must be at least 8 characters"]
        #         }
        #     },
        #     "meta": {
        #         "timestamp": "2023-01-01T00:00:00Z",
        #         "version": "1.0"
        #     }
        # }

        # TODO: #2
        # // ОШИБКА ВАЛИДАЦИИ
        # {
        #     "status": "error",
        #     "error": {
        #         "code": "VALIDATION_FAILED",
        #         "message": "The form contains invalid data",
        #         "details": {
        #             "email": ["Must be valid email"],
        #             "password": [
        #                 "Must be at least 8 characters",
        #                 "Must contain uppercase letter"
        #             ]
        #         }
        #     },
        #     "message": "Please check your input",
        #     "meta": {
        #         "timestamp": "2023-01-01T00:00:00Z",
        #         "version": "1.0"
        #     }
        # }

        parent::__construct([
            'status' => 'error',
            'errors' => $errors, // TODO: -> error - и тянуть details
            'message' => $message,
        ], $statusCode);
    }
}
