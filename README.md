# graphql-php-sqlite
A basic graphql server using the graphql-php package that works with a sqlite database. 

### Setup 

Instructions assume your are running MAMP (or alike) but MAMP is certainly not required. Feel free to use a PHP environment of your choice. 

1. Clone the repo in htdocs
2. Make sure you have composer installed. Navigate to the project folder and run ```composer install```
3. Fire up MAMP (or your favourite PHP environment) and open API url in the browser. It should look something like: 
```http://localhost:<port>/graphql-php-sqlite/server/api.php``` and throw a JSON error message: 
```
{
  errors: [
    {
      message: "GraphQL Request must include at least one of those two parameters: "query" or "queryId"",
      category: "request"
    }
  ]
} 
```
That's ok, it is expected behaviour. 

4. Now test if the sqlite DB connection is working. Navigate to: ```http://localhost:<port>/graphql-php-sqlite/db/db-test.php```
If you see a response like the following:
```
Array
(
    [0] => Array
        (
            [id] => 1
            [firstName] => Shahab
            [lastName] => Qamar
            [email] => sqamar@test.net
        )

    [1] => Array
        (
            [id] => 2
            [firstName] => Andrew
            [lastName] => Byrne
            [email] => abyrne@test.net
        )

    [2] => Array
        (
            [id] => 3
            [firstName] => Scott
            [lastName] => Hall
            [email] => shall@test.net
        )

)
```
congratulations, you are all set. If you are getting a 500 internal server error, check if your PHP installation has sqlite enabled.

5. Install and launch the ChromeiQL extension for Chrome `https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij` which is an implementation of GraphiQL client. 

6. In the ChromeiQL UI, set `http://localhost:<port>/graphql-php-sqlite/server/api.php` as your endpoint. At the top right, click to expand the Docs section. You should see the following root types: 
```
query: Query
mutation: mutation
```
Awesome! thats all to it. Lets run an example query. Type the following query and hit play:
```
{
  user(id:1) {
    firstName
    email
  }
}
```
You should get something like:
```
{
  "data": {
    "user": {
      "firstName": "Shahab",
      "email": "sqamar@test.net"
    }
  }
}
```

## Useful links
* http://webonyx.github.io/graphql-php/
* http://graphql.org/learn/
* https://www.howtographql.com/
* https://www.sqlite.org/
* http://sqlitebrowser.org/ (handy GUI tool to browse and edit sqlite DB)
