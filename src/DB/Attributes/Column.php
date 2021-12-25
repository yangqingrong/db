<?php

 

namespace DB\Attributes;

/**
 * Description of Column
 *
 * @author YangQing-rong
 */
#[Attribute]
class Column {
    public function __construct(
            protected string $name,
            protected string $type,
            protected string $length ,
            protected string $default,
            protected bool $null = false ,
            protected bool $visible = true,
            protected string $comment = ''
    ) {
        
    }
    
    public function name() {
        return $this->name;
    }
    
    public function type() {
        return $this->type;
    }
    
    public function length() {
        return $this->length;
    }
    
    public function comment() {
        return $this->comment;
    }
    
    public function defaultValue() {
        return $this->default;
    }
    
    public function isNull() {
        return $this->null;
    }
    
    public function visible( ) {
        return $this->visible;
    }
}
