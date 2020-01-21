# Order API

API for list, place and take orders.

### Prerequisites

* [Docker](https://docs.docker.com/install/) - Docker provides a way to run applications securely isolated in a container, packaged with all its dependencies and libraries.
* Git

### Installation and run

1. Take a clone from repository by running below command. 
```
git clone https://github.com/Arvindkumar-01/OrdersApi.git
```
2. Go to `src` folder. This folder contains all source code of application. 
3. We are using Google distance matrix api in this application, so you need to set API key for using google API. You need to set
`GOOGLE_MAP_API_KEY="YOUR_API_KEY"` in .env file located in `src` folder. You can create a new API Key from https://cloud.google.com/maps-platform/routes. 

4. Run `start.sh` file located at root of directory. This script file bhild and run docker container, install required composer packages, run application migrations and run test cases. 

5. Server is accessible on `http://localhost:8080` .  

## Test Cases
All the test cases are written on `phpunit`.

### Manually running the tests

1. Running unit test cases `docker exec app vendor/bin/phpunit tests/Unit/`
2. Running feature test cases `docker exec app vendor/bin/phpunit tests/Feature/`

## Documentation

We are using **Swagger/OpenApi** for generating documentation of API. You can access documentation on `http://localhost:8080/api/documentation`

## API Documentation 

1. Place order API
    * Url `http://localhost:8080/api/v1/orders`
    * Method `POST`
    * Headers `Content-Type:application/json`
    * Request Body 
    ```sh {
      "origin": [
          "40.6655101",
          "-93.89188969999998"
      ],
      "destination": [
          "40.6905615",
          "-73.9976592"
      ]
   }
    ```
   
   * Response 
     ```sh{
          'id':1,
          'distance':10434,
          'status':'UNASSIGNED'
         }
     ```
  2. Order List    
       * Url `http://localhost:8080/api/v1/orders/page=1&limit=5`
       * Method `GET`
       * Headers `Content-Type:application/json`     
       * Response 
        ```sh 
            [
            {'id':1,'distance':10,'status':'UNASSIGNED'},
            {'id':2,'distance':10,'status':'UNASSIGNED'}
            ]         
        ```
   3. Take order
       * Url `http://localhost:8080/api/v1/orders/3`
       * Method `PATCH`
       * Headers `Content-Type:application/json`
       * Request Body
       ```sh 
          { "status": "TAKEN"}
       ```
       * Response 
        ```sh 
         {"status":"SUCCESS"}
        ```
