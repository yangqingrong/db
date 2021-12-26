<?php

namespace DB;

class Model {

    protected $primaryKey = 'id';
    
     

    public static function __callStatic($a, $b) {
        
        
        
        $m = new static;
        
       
        $table = ReflectionHelper::class_attr_arg( static::class , 'DB\Attributes\Table',  'name' );
        $connection = ReflectionHelper::class_attr_arg( static::class , 'DB\Attributes\Table',  'connection' );
        if( $connection == null){
            $connection = 'default';
        }
        $alias = ReflectionHelper::class_attr_arg( static::class , 'DB\Attributes\Table',  'alias' );
        if( $alias == null){
            $alias = $table;
        }
        
        $s = S::c($connection )->t($table . ' AS ' . $alias )->pk($m->primaryKey);
        return call_user_func_array([$s, $a], $b);
    }

}

?>