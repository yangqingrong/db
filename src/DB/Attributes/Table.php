<?php
                     
 

namespace DB\Attributes;
use \Attribute;
 
#[Attribute]
class Table {
    public function __construct(
            //table name
            public ?string $name = null,
             public ?string $alias = null,
             public ?string $connection = null 
          
    ) {
        
    }
    
    //class name
    public function getName(){
        return self::class;
    }
     public function getArguments()  {
       return   get_object_vars( $this);
         
    }
}
