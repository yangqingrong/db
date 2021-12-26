<?php
 

namespace DB;

 
class ReflectionHelper {
    public static function class_attr_arg($class , $attr_class , $arg_name ) {
        $rf =new \ReflectionClass( $class );
       $attrs = $rf->getAttributes();
       foreach ( $attrs as $attr){
          //$attr = $attr->newInstance();
            
           if( $attr->getName() ==  $attr_class ){
               $args = $attr->getArguments();
               if( isset( $args[$arg_name] )){
                    return   $args[$arg_name] ;
               }
           }
       }
       return  null;
    }
}
