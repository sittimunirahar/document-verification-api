## Document Verification API

### Description: 

Verifies uploaded document in JSON and receives a verification result as a response.

#### HTTP Method: 

    POST

#### API Endpoint URL: 

  `POST /api/document_verification`

#### Request Parameter:
  - `file` (required, type: json and size: <= 2MB): Uploaded file

#### Response Body:

- Status Code: 200 OK
  
    Content-Type: application/json

    ```
    Body:
    {
      "issuer": "Accredify",
      "result": "verified" // "verified", "invalid_recipient", "invalid_issuer", or "invalid_signature"
    }
    ```

- Status Code: 401 Unauthorized
    
    Content-Type: application/json

    ```
    Body:
    {
      "error": "User not found"
    }
    ```

- Status Code: 422 Unprocessable entity
    
    Content-Type: application/json

    ```
    Body:
    {
      'message' => 'The file field must not be greater than 2048 kilobytes.',
        'errors' => [
          'file' => [
            'The file field must not be greater than 2048 kilobytes.'
          ]
        ]
    }
    ```

- Status Code: 422 Unprocessable entity
    
    Content-Type: application/json

    ```
    Body:
    {
      'message' => 'The file field must be a file of type: json.',
        'errors' => [
          'file' => [
            'The file field must be a file of type: json.'
          ]
        ]
    }
    ```