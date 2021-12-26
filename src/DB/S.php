<?php

namespace DB;

use PDO;

class S {

    protected $c; //con
    protected $cfg;
    protected static $cf; //all
    protected static $s = []; //insts
    protected $pk = 'id';
    protected $_s = '*';
    protected $_f = [];
    protected $_j = '';
    protected $_w = '';
    protected $_ob = '';
    protected $_l = '';
    protected $_pr = [];

    public function __construct($cf) {


        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        if (version_compare(PHP_VERSION, "5.3.6", "<=")) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8;";
        }

        try {
            $this->c = new PDO($cf["d"], $cf["u"], $cf["p"], $options);
            if (version_compare(PHP_VERSION, "5.3.6", "<=")) {
                $this->c->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
            $this->c->exec("set names " . $cf['c']);
        } catch (PDOException $e) {
            die("Error!: " . $e->getMessage() . "<br/>");
        }
        $this->cfg = $cf;
    }

    //cfg
    public static function config($cf) {
        self::$cf = $cf;
    }

    //conn
    public static function c($c = 'default') {
        if( empty(self::$cf[$c])){
            throw new \Exception('Can not found connection name "'. $c .'"  in config/database.php,use "default"  for instead?');
        }
        if (!isset(self::$s[$c])) {
            self::$s[$c] = new S(self::$cf[$c]);
        }
        
        return self::$s[$c];
    }

    //parse table
    public function pt($tb) {

        if (is_string($tb)) {
            $t = '';
            $n = $tb;
            $a = '';
            $tb = trim($tb);
            if (strpos($tb, ' ') !== false) {
                $tb = preg_replace('#(\s+as\s+)|\s+#i', '|', $tb);
                list($n, $a) = explode('|', $tb);
            }

            if (strpos($n, '.') !== false) {
                $t = $n;
            } elseif (strpos($n, ':') !== false) {
                list($d, $t) = explode(':', $n);
                $t = $d . '.' . $this->cfg["pf"] . $t;
            } else {
                $t = $this->cfg["pf"] . $n;
            }

            return [$t, $a];
        }
    }

    //add to _f
    public function t($n) {
        //print_r($n);
        $this->_f[] = $this->pt($n);
        return $this;
    }

    //join
    public function pts($n) {
        //print_r( $n);
        $r = $this->pt($n);
        $f = $r[0];
        if (trim($r[1]) != '') {
            $f .= ' AS ' . $r[1];
        }

        return $f;
    }

    //from
    protected function _f() {
        $a = [];
        foreach ($this->_f as $f) {
            $a[] = $f[0] . ' ' . $f[1];
        }
        return implode(',', $a);
    }

    //i u d
    public function _ft() {
//	 print_r($this->_f);
        return $this->_f[0][0];
    }

    public function j($t, $n, $f1, $f2) {
        $this->_j .= ' ' . $t . ' JOIN ' . $this->pts($n) . ' ON ' . $f1 . '=' . $f2 . ' ';
        return $this;
    }

    public function lj($n, $f1, $f2) {
        return $this->j('LEFT', $n, $f1, $f2);
    }

    public function pk($s) {
        $this->pk = $s;
        return $this;
    }

    public function s($s) {
        $this->_s = $s;
        return $this;
    }

    public function l($o, $r) {
        $this->_l = intval($o) . ',' . intval($r);
        return $this;
    }

    public function w($w, $o, $v = null, $op = 'AND') {
        if (strlen($this->_w) > 0) {
            $this->_w .= ' ' . $op . ' ';
        }
        if ($v === null) {
            $this->_w .= $w . ' =  ? ';
            $this->_pr[] = $o;
        } else {
            $this->_w .= $w . ' ' . $o . ' ? ';
            $this->_pr[] = $v;
        }


        return $this;
    }

    public function aw($w, $o, $v = null) {
        return $this->w($w, $o, $v);
    }

    public function ow($w, $o, $v = null) {
        return $this->w($w, $o, $v, 'OR');
    }

    public function wr($w, ...$a) {
        if (strlen($this->_w) > 0) {
            $this->_w .= ' AND ';
        }
        $this->_w .= ' ' . $w . ' ';
        foreach ($a as $v) {
            $this->_pr[] = $v;
        }
        return $this;
    }

    public function awr($w, ...$a) {

        return $this->wr($w, ...$a);
    }

    public function owr($w, ...$a) {
        if (strlen($this->_w) > 0) {
            $this->_w .= ' OR ';
        }
        return $this->wr($w, ...$a);
    }

    public function f($id, $k = 'id') {
        return $this->w($k, $id)->g();
    }

    public function ob($o) {
        $this->_ob = $o;
        return $this;
    }

    public function exec($sql, $args = []) {

        $stmt = null;
        try {
            $stmt = $this->c->prepare($sql);

            $stmt->execute($args);
        } catch (\PDOException $ex) {
            echo $ex->getMessage();
        }
        if (isset($this->cfg["dbg"])) {
            echo "<div>";

            echo $sql;
            print_r($args);
            echo "</div>";
            $errInfo = $stmt->errorInfo();
            if (intval($errInfo [0]) > 0) {
                print_r($errInfo);
            }
        }
        return $stmt;
    }

    public function ga($fst = false) {
        $sql = 'SELECT ' . $this->_s . ' FROM ' . $this->_f();
        if (trim($this->_j) != '') {
            $sql .= ' ' . $this->_j . ' ';
        }
        if (trim($this->_w) != '') {
            $sql .= ' WHERE ' . $this->_w . ' ';
        }
        if (trim($this->_ob) != '') {
            $sql .= ' ORDER BY ' . $this->_ob . ' ';
        }
        if ($fst) {
            $sql .= ' LIMIT 0,1 ';
        } elseif (trim($this->_l) != '') {
            $sql .= ' LIMIT  ' . $this->_l;
        }
        $dt = array();
        $stmt = $this->exec($sql, $this->_pr);
        try {



            while ($r = $stmt->fetch(PDO::FETCH_OBJ)) {
                $dt [] = $r;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        // print_r( $dt );echo $sql;
        $this->cl();
        return $dt;
    }

    public function g() {
        $dt = $this->ga(true);
        return @$dt [0];
    }

    protected function cl() {
        $this->pk = 'id';
        $this->_s = '*';
        $this->_f = [];
        $this->_j = '';
        $this->_w = '';
        $this->_ob = '';
        $this->_l = '';
        $this->_pr = [];
    }

    public function i($r) {
        $va = $f = $p = array();
        foreach ($r as $k => $v) {

            if (is_scalar($v)) {
                $va [] = '?';
                $f [] = '`' . $k . '`';
                $p[] = $v . "";
            }
        }


        $sql = 'INSERT INTO ' . $this->_ft() . '(' . implode(',', $f) . ') VALUES(' . implode(',', $va) . ')';

        if ($this->exec($sql, $p)) {
            $id = $this->c->lastInsertId();
            $this->cl();
            return $id;
        } else {
            return false;
        }
    }

    public function u($r, $lmt1 = false) {

        $args = $assignArray = array();
        $sql = 'UPDATE ' . $this->_ft() . ' SET ';
        foreach ($r as $key => $val) {
            $assignArray[] = '`' . $key . '`' . '=?';
            $args[] = $val;
        }
        $sql .= implode(",", $assignArray);
        $sql .= ' WHERE ' . $this->_w;
        if ($lmt1) {
            $sql .= ' LIMIT 0,1 ';
        }
        for ($i = 0; $i < count($this->_pr); $i++) {
            $args[] = $this->_pr[$i];
        }
        //echo $sql;
        if ($stmt = $this->exec($sql, $args)) {
            $this->cl();
            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function d($lmt1 = false) {

        $sql = 'delete from ' . $this->_ft() . ' where ' . $this->_w;
        if ($lmt1) {
            $sql .= ' LIMIT 0,1 ';
        }
        $stmt = $this->exec($sql, $this->_pr);
        $this->cl();
        return $stmt->rowCount();
    }

    public function cnt() {
        $r = $this->s('count(*) as cnt')->ob('')->g();
        return $r->cnt;
    }

    public function p($pp = 15, $p = null) {
        $sc = clone $this;
        $t = $sc->cnt();


        $last = ceil($t / $pp);

        if ($p == null) {
            $p = intval(@$_GET['page']);
            if ($p == 0) {
                $p = 1;
            }
        }
        if ($p > $last) {
            $p = $last;
        }
        if ($p < 1) {
            $p = 1;
        }

        $ofs = ( $p - 1) * $pp;

        $this->l($ofs, $pp);
        $dt = $this->ga();
        return [
            't' => $t,
            'p' => $p,
            'pp' => $pp,
            'l' => $last,
            'd' => $dt,
        ];
    }

}
