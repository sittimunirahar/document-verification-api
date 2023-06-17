## Document Verification API
- [Document Verification API](#document-verification-api)
  - [About](#about)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Running Test](#running-test)
  - [Other Documentations](#other-documentations)

***
### About

This project was created for Accredify's Technical Assessment.

The feature demonstrates a document verification API where an authenticated user sends a JSON file and receives a verification result as a response.

The document is verified based on 3 conditions: valid issuer, recipient and signature. 

Tech Stack: PHP 8, Laravel 10, MySQL, Docker: Laravel Sail, PHP Pest.

### Prerequisites

- Git
- **If you are using Sail for local development:** Docker Desktop
- **If you are not using Docker:** PHP, Composer and a database

***

### Installation

1. Clone this repository:

        git clone https://github.com/sittimunirahar/document-verification-api.git

2. Navigate to the Project Directory:
   
        cd document-verification-api

3. Create and configure `.env` File:

   - Rename the `.env.example` file in the project root directory to `.env`
   - Update the necessary environment variables in the `.env` file, such as database credentials, app key, etc.

4. Build and Run the project:
  
    **Sail** 
    
    Build and Run the Docker Containers:
    - Run the following command to start Sail:
      `./vendor/bin/sail up`

    - Sail will build and run the necessary Docker containers based on the project's `docker-compose.yml` configuration.

    **Without Sail**
    
    As mentioned in the Prerequisites, ensure that you have the required dependencies (PHP, Composer, and a database) installed locally. 
    - Run the following command to install project dependencies:
    `composer install`
    - Start your local development server (e.g., Apache, Nginx) and configure it to point to the project's `public` directory.

5. Run Database Migrations:
   
   **Sail**
   
   - Run the following command to access the application container:
      `./vendor/bin/sail shell`

   - Run the migrations:
      `php artisan migrate`

   - You can also directly run without running the `./vendor/bin/sail shell` command: 
  
      `sail artisan migrate`

   **Without Sail**

     - Create your database 
     - Configure it in the `.env`
     - Run the migrations:
   
         `php artisan migrate`

6. Access the application:

    **Sail**

    - The application should now be accessible at http://localhost or http://127.0.0.1 in your browser.

    **Without Sail**

    - Visit the configured URL for your local development server (e.g., http://localhost or http://127.0.0.1) in your browser.

***

### Running Test

1. Running Test
   
    **Sail (Without application container access):**

    
        ./vendor/bin/sail test --filter "<TestName>"

    or 

        sail artisan test --filter <TestName>

    **Sail (with application container access) or Without Sail:**

        php artisan test --filter <TestName> 

    *Optional*
    * Add `--coverage` to run coverage
    * Remove `TestName` to run all test


2. Feature test (covers the end-to-end document verification process)

    **Test name:** DocumentVerificationTest

    **Test covers:**
    - authorized user is able to verify document      
    - authorized user is able to verify document with incomplete data
    - authorized user is not allowed to upload file bigger than 2MB
    - authorized user is only allowed to upload file in json
    - unauthorized user is not allowed to verify document
    - stores verification result in database

3. Unit tests
   
   **Test name:** DocumentVerificationServiceTest

   **Test covers:**
   - transforms file content to JSON
   - verifies document
   - formats verification data
   - stores verification results 

   **Test name:** DocumentValidationServiceTest

   **Test covers:**
   Tests\Unit\DocumentValidatorServiceTest
   - validates document with complete data
   - validates recipient with missing data
   - validates issuer with missing data
   - validates signature with missing data

***

### Other Documentations

 - Refactoring or Improvement Plan: `ROADMAP.md`
 - Document Verification API documentation: `docs/document_verification_api.md`
 - Technical documentations: in `docs/`
    1. `Sequence Diagram.png`
    2. `Verification Flowchart.png`
    3. `Architecture Comparison Table.png`
