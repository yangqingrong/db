<?php

 

namespace DB\Attributes;

/**
 * Description of Table
 *
 * @author YangQing-rong
 */
#[Attribute]
class Table {
    public function __construct(
            protected string $name
    ) {
        
    }
    
    public function name( ) {
        return $this->name;
    }
}
