# gophry-dto

Gophry-dto is a simple library that helps you to transfer data between your PHP application layers

## Installation

You can install the package with composer ([learn more](https://getcomposer.org/doc/01-basic-usage.md)).

```
composer require norbertkranitz/gophry-dto "dev-master"
```

## RequestDTOs

Most of the PHP frameworks provide the request data in an array. Working with arrays is good, but what if we wrap/adapt this array into a simple object, so we can do some stuff on it, like validating?

The ```\Gophry\DTO\RequestDTO``` abstract class implements a ```bind(array $values)``` method, which can bind the passed array data to the properties with the same name.

```PHP
class LoginRequestDTO extends \Gophry\DTO\RequestDTO {

    protected $email;
    
    protected $password;
        
    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

}

$data = [
    'email' => 'test@email.app',
    'password' => 'password'
];

$dto = new LoginRequestDTO();
$dto->bind($data);

echo $dto->getEmail(); 
//'test@email.app'

```

Also you can define object trees based on the DTO also the data array structure.

```PHP
class NameRequestDTO extends \Gophry\DTO\RequestDTO {

    protected $first_name;
    
    protected $last_name;
        
    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

}

class RegisterRequestDTO extends \Gophry\DTO\RequestDTO {

    protected $email;
    
    protected $password;

    protected $name;

    //Important!
    public function __construct() {
        $this->name = new NameRequestDTO();
    }
        
    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getName() {
        return $this->name->getFirstName() . ' ' . $this->name->getLastName();
    }

}

$data = [
    'name' => [
        'first_name' => 'First',
        'last_name' => 'Last'
    ],
    'email' => 'test@email.app',
    'password' => 'password'
];

$dto = new RegisterRequestDTO();
$dto->bind($data);

echo $dto->getName(); 
//'First Last'
```

> Please note that the sub request DTO must have a request DTO value too to make it work

## ResponeDTOs

Most of the PHP frameworks require an array result - or an object result based an array value - as the result of an action. That's why we created the ```\Gophry\DTO\ResponseDTO``` class.

The extension of this class has almost the same functionality as the ```\Gophry\DTO\RequestDTO```. The differences are this class prefers the usage of setters and it implements a method called ```toArray```.

```PHP
class LoginResponseDTO extends \Gophry\DTO\ResponseDTO {

    protected $token;
    
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

}

$dto = new LoginResponseDTO();
$dto->setToken('theToken');

print_r($dto->getEmail()); 
//['token' => 'theToken']
```

You have the option to work wih ResponseDTO object trees too.


```PHP
class NameResponseDTO extends \Gophry\DTO\ResponseDTO {

    protected $first_name;
    
    protected $last_name;
        
    public function setFirstName($first_name) {
        $this->first_name = $first_name;
        return $this;
    }

    public function setFirstName($last_name) {
        $this->last_name = $last_name;
        return $this;
    }

}

class LoginResponseDTO extends \Gophry\DTO\ResponseDTO {

    protected $token;

    protected $name;
    
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function setName(NameResponseDTO $name) {
        $this->name = $name;
        return $this;
    }

}

$name = new NameResponseDTO();
$name->setFirstName('First');
$name->setLastName('Last');

$dto = new LoginResponseDTO();
$dto->setToken('theToken');
$dto->setName($name);

print_r($dto->getEmail()); 
//[
//  'token' => 'theToken'
//  'name' => [
//      'first_name' => 'First',
//      'last_name' => 'Last'
//  ]
//]
```

> It makes sense to use the Request and the Response phrases in the class names 

## How to use?

Assume that you have a controller, and an action which is currently triggered. The controller provides the request data as an array, also requires an array result. 

```PHP

class AuthService {

    public static function login(LoginRequestDTO $dto) {
        //do something with the $dto;
        //communicate with database
        //log something
        //send a welcome mail
        //throw an exception
        //catch the exception
        //etc
        $result = new LoginResponseDTO();
        $result->setToken($token);
        return $result;
    }

}

...

class AuthController extends BaseController {
    
    public function loginAction() {
        $data = $this->request->get();
        $dto = new LoginRequestDTO();
        $dto->bind($data);
        Validator::validate($dto);
        return AuthService::login($dto)->toArray();
    }

}
```
