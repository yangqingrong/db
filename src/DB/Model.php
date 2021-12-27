<?php

namespace DB;

class Model {

    protected $primaryKey = 'id';
    
     

    public static function __callStatic($a, $b) {
        
        
        
        $m = new static;
        
       
        $args = ReflectionHelper::class_attr_args( static::class , 'DB\Attributes\Table'  );
        $table = $args['name'];
        $connection =  $args['connection']  ?? 'default';
        
        $alias = $args['alias']  ?? $table;
        
        $s = S::c($connection )->t($table . ' AS ' . $alias )->pk($m->primaryKey);
        return call_user_func_array([$s, $a], $b);
    }

}

?>