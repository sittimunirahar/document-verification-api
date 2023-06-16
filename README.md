### Prerequisites

- Git
- **If you are using Sail for local development:** Docker Desktop
- **If you are not using Docker:** PHP, Composer and a database

### Installation

1. Clone this repository:

    `git clone https://github.com/sittimunirahar/document-verification-api.git`

2. Navigate to the Project Directory:
   
    `cd document-verification-api`

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
    
    As mentioned in the Prerequisites, ensure you have the required dependencies (PHP, Composer, and a database) installed locally. 
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
     - Configure it in the .env
     - Run the migrations:
   
      `php artisan migrate`

6. Access the application:

    **Sail**

    - The application should now be accessible at http://localhost or http://127.0.0.1 in your browser.

    **Without Sail**

    - Visit the configured URL for your local development server (e.g., http://localhost or http://127.0.0.1) in your browser.

### Running Test

1. Run feature test (covers the end-to-end document verification process)

    **Sail (Without application container access):**

    `./vendor/bin/sail test --filter "DocumentVerificationTest"`

    or 

    `sail artisan test --filter DocumentVerificationTest`

    **Sail (with application container access) or Without Sail:**

    `php artisan test --filter DocumentVerificationTest`

    **Test covers:**
    - authenticated user successfully verify document      
    - authenticated user successfully verify document with incomplete data
    - unauthenticated user cannot verify any document
    - authenticated user only can submit file bigger than 2MB
    - authenticated user only can submit file in json   
    - verification result stored in database

2. Run unit test

### Other Documentations

 - Refactoring or Improvement Plan: `ROADMAP.md`
 - Documentations:
   - Document Verification API documentation: `docs/document_verification_api.md`
   - Technical documentation: `docs/technical_documentation.md`