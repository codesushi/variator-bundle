A Symfony bundle wrapper for coshi/variator library
-------------------------------------------------------

Allows to specify callbacks with services:
  
````
    'text' => [
        'type' => 'int',
        'min' => 0,
        'max' => ['@some_service', 'someMethod']
````

Brings new variation type: "iteratorResult" to operate over the Doctrine IterableResult

Usage:

````
    $builder = $container->get('coshi.variator_bundle.builder');
    
    $config = [
        'id' => [
            'type' => 'iteratorResult',
            'callback' => ['@some_repository', 'getSomeIds'],
        ],
    ];
    $variations = $builder->build($config);
    
    foreach ($variations as $values) {
        foreach($values as $value) {
            print($value); // displays ids one by one            
        }
    }
````

You can strip values to several chunks. Variator will then fetch the data using LIMIT and OFFSET SQL statements:

````
    $builder = $container->get('coshi.variator_bundle.builder');
    
    $config = [
        'id' => [
            'type' => 'iteratorResult',
            'callback' => ['@some_repository', 'getSomeIds'],
            'chunked' => true,
            'chunk_size' => 100
        ],
    ];
    $variations = $builder->build($config);
    
    foreach ($variations as $values) {
        foreach($values as $value) {
            print($value); // displays ids one by one, same as in previous            
        }
    }
````