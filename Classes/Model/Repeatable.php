<?php
namespace Mireo\RepeatableFields\Model;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMapper;

/**
 * Repeatable
 *
 * @api
 */
class Repeatable implements \Iterator, \JsonSerializable{

    /**
     * @var PropertyMapper
     */
    protected $propertyMapper;

    /** @var array */
    protected $convertedFields;

    /** @var array */
    private $nestedFields;

    /** @var array */
    private $source;

    /** @var array */
    private $fieldsDeclaration;

    /**
     * @var int
     */
    private $position = 0;

    public function __construct($source, $fieldsDeclaration) {
        $this->setSource($source);
        $this->fieldsDeclaration = $fieldsDeclaration;
    }

    /**
     * @param PropertyMapper $propertyMapper
     */
    public function injectPropertyMapper($propertyMapper){
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @param $types
     * @throws \Neos\Flow\Property\Exception
     * @throws \Neos\Flow\Security\Exception
     */
    public function initialize()
    {
        $convertedProps = [];
        $byIndexes = [];
        if ($this->source){
            foreach ($this->source as $key => $group) {
                foreach ($group as $index => $val) {
                    if( !isset($this->fieldsDeclaration[$index]) )
                        continue;
                    if ($val) {
                        $conf = $this->fieldsDeclaration[$index];
                        $target = $conf['type'] ?? 'string';
                        $v = $this->propertyMapper->convert($val, $target);
                    } else {
                        $v = $val;
                    }
                    $byIndexes[$index][] = $v;
                    $convertedProps[$key][$index] = $v;
                }
            }
        }
        $this->convertedFields = $convertedProps;
        $this->nestedFields = $byIndexes;
    }

    public function convertedFields(){
        return $this->convertedFields;
    }

    public function getSource(){
        return $this->source;
    }

    public function setSource($source){
        $this->source = $source;
    }

    public function nestedField($field){
        return isset($this->nestedFields[$field])?$this->nestedFields[$field]:null;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(){
        return $this->convertedFields[$this->position];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(){
        $this->position++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(){
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(){
        if( isset($this->convertedFields[$this->position]) )
            return true;
        return false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(){
        $this->position = 0;
    }

    public function jsonSerialize()
    {
        return $this->source;
    }
}