<?php
 

namespace DB;

 use DB\Attributes\Table;
class ReflectionHelper {
    
    
    public static function class_attr_args($class , $attr_class  ) {
        $rf =new \ReflectionClass(  $class );
       $attrs = $rf->getAttributes();
       foreach ( $attrs as $attr){
            $attr = $attr->newInstance();
            
           if( $attr->getName() ==  $attr_class ){
               $args = $attr->getArguments();
               
                    return   $args  ;
               
           }
       }
       return  null;
    }
}
