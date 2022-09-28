# uuu9-cors
#CORS Middleware for Lumen


##Installation (Lumen 5.3)

`bootstrap.php`

add:

```
$app->register(U9\Cors\Middleware\CorsServiceProvider::class);
```

and

```
$app->routeMiddleware([
    ......
    'cors' => U9\Cors\Middleware\Cors::class,
]);
```

##Attention Please !

This Middleware Will Handle All Options Request.

Only for `uuu9.cn` & `uuu9.com`

##Installation (Lumen 5.4)


```

$app->middleware([
    'cors'=>U9\Cors\Middleware\Cors::class,

]);

$app->routeMiddleware([
    ......
    'cors' => U9\Cors\Middleware\Cors::class,
]);
```
