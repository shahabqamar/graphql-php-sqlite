<?php
require_once __DIR__ . '../../vendor/autoload.php';

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;
use GraphQL\Server\StandardServer;

try {

    $userType = new ObjectType([
      'name' => 'user',
      //use callable syntax as versionType is not defined yet
      //read more here: http://webonyx.github.io/graphql-php/type-system/object-types/#recurring-and-circular-types
      'fields' => function() use (&$fileType) {
        return  [
          'id' => ['type' => Type::int()],
          'firstName' => ['type' => Type::string()],
          'lastName' => ['type' => Type::string()],
          'email' => ['type' => Type::string()],
          'files' => [
              'type' => Type::listOf($fileType),
              'resolve' => function ($root, $args) {

                $db = new SQLite3('../db/data.db');
                $results = $db->query('SELECT * FROM file where userId='.$root['id']);
                $resultArr = [];
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                  $resultArr[] = $row;
                }

                return $resultArr;
              }
            ]
          ];
      }
    ]);

    $versionType = new ObjectType([
      'name' => 'version',
      'description' => 'Object of type version',
      'fields' =>  [
          'id' => ['type' => Type::id()],
          'name' => ['type' => Type::string()],
          'mimetype' => ['type' => Type::string()],
          'url' => ['type' => Type::string()],
          'size' => ['type' => Type::int()],
          'created' => ['type' => Type::int()],
          
          //resolve version user
          'user' => [
            'type' => $userType,
            'resolve' => function($root, $args) {
              $db = new SQLite3('../db/data.db');
              $results = $db->query('SELECT * FROM user where id='.$root['userId']);
              $resultArr = [];
              while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $resultArr[] = $row;
              }

              // $file = 'log.txt';
              // $current = file_get_contents($file);
              // file_put_contents($file, print_r($resultArr, true));
    
              return $resultArr[0];
            }
          ],
        ],
    ]); 

    $fileType = new ObjectType([
      'name' => 'file',
      'description' => 'Object of type File',
      'fields' =>  [
          'id' => ['type' => Type::id()],
          'name' => ['type' => Type::string()],
          'folderId' => ['type' => Type::id()],
          
          //resolve file user
          'user' => [
            'type' => $userType,
            'resolve' => function($root, $args) {
              $db = new SQLite3('../db/data.db');
              $results = $db->query('SELECT * FROM user where id='.$root['userId']);
              $resultArr = [];
              while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $resultArr[] = $row;
              }

              // $file = 'log.txt';
              // $current = file_get_contents($file);
              // file_put_contents($file, print_r($resultArr, true));
    
              return $resultArr[0];
            }
          ],
          'versions' => [
            'type' => Type::listOf($versionType),
            'resolve' => function ($root, $args) {
              
              $db = new SQLite3('../db/data.db');
              $results = $db->query('SELECT * FROM version where fileId='.$root['id']);
              $resultArr = [];
              while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $resultArr[] = $row;
              }
              
              // $file = 'log.txt';
              // $current = file_get_contents($file);
              // file_put_contents($file, print_r($resultArr, true));

              return $resultArr;
            }
          ]
        ]
    ]); 

    $queryType = new ObjectType([
      'name' => 'Query',
      'fields' => [

        //get single user
        'user' => [
          'type' => $userType,
          'args' => [
            'id' => ['type' => Type::nonNull(Type::int())],
          ],
          'resolve' => function ($root, $args) {

            $db = new SQLite3('../db/data.db');
            $results = $db->query('SELECT * FROM user where id='.$args['id']);
        
            $resultArr = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
              $resultArr[] = $row;
            }

            return $resultArr[0];
          }
        ],

        //get single file
        'file' => [
          'type' => $fileType,
          'args' => [
            'id' => ['type' => Type::nonNull(Type::int())],
          ],
          'resolve' => function ($root, $args) {

            $db = new SQLite3('../db/data.db');
            $results = $db->query('SELECT * FROM file where id='.$args['id']);
        
            $resultArr = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
              $resultArr[] = $row;
            }

            return $resultArr[0];
          }
        ],

        //get single version
        'version' => [
          'type' => $versionType,
          'args' => [
            'id' => ['type' => Type::nonNull(Type::int())],
          ],
          'resolve' => function ($root, $args) {

            $db = new SQLite3('../db/data.db');
            $results = $db->query('SELECT * FROM version where id='.$args['id']);
        
            $resultArr = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
              $resultArr[] = $row;
            }

            return $resultArr[0];
          }
        ],
      ],
    ]);

    //mutations
    $mutationType = new ObjectType([
      'name' => 'mutation',
      'fields' => [

        //add a user
        'addUser' => [
          'type' => $userType,
          'args' => [
            'firstName' => ['type' => Type::nonNull(Type::string())],
            'lastName' => ['type' => Type::nonNull(Type::string())],
            'email' => ['type' => Type::nonNull(Type::string())],
          ],
          'resolve' => function ($root, $args) {
          
            $db = new SQLite3('../db/data.db');
            $results = $db->query("INSERT INTO user (firstName, lastName, email) VALUES ('".$args['firstName']."', '".$args['lastName']."', '".$args['email']."');");
            $results = $db->query("SELECT * FROM USER WHERE id=last_insert_rowid();");
            $resultArr = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
              $resultArr[] = $row;
            }

            // $file = 'log.txt';
            // $current = file_get_contents($file);
            // file_put_contents($file, print_r($resultArr, true));

            return $resultArr[0];
          },
        ],

        //delete a user
        'deleteUser' => [
          'type' => $userType,
          'args' => [
            'id' => ['type' => Type::nonNull(Type::id())]
          ],
          'resolve' => function ($root, $args) {
            $db = new SQLite3('../db/data.db');
            $results = $db->query("DELETE FROM user WHERE id=".$args['id']);
            return null;
          },
        ],
      ],
    ]);

    // See docs on schema options:
    // http://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
      'query' => $queryType,
      'mutation' => $mutationType,
    ]);

    // See docs on server options:
    // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
    $server = new StandardServer([
        'schema' => $schema
    ]);

    $server->handleRequest();

} catch (\Exception $e) {
    StandardServer::send500Error($e);
}