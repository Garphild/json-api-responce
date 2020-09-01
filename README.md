# Concepts

Centralized managment of json response.

Singleton.

Can be multiple managers, but why?  

## Default response structure
```
{
  'status': <number>,
  'data': <array>,
  'errors': <array>
}
```

## Customize structure
```
class AnotherResponseModel implements \Garphild\ApiResponse\IResponseModel {
  ...
}
$manager = \Garphild\ApiResponse\ApiResponseManager::instance();
$manager->changeResponseModel(new AnotherResponseModel());
```

# Functions
## add data to responce
```
$manager->setField('name', 123);
```
## get current data
```
$manager->getField('name');
```
```
$manager->getData();
```
## send data to client
```
$manager->send();
```

```
$manager->finalize();
```
## work with status codes
```
$manager->forbidden();
```

```
$manager->notFound();
```

```
$manager->badRequest();
```

```
$manager->terminateWithHttpCode(200);
```
