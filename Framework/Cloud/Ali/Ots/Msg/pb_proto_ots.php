<?php
/**
 * Auto generated from ots.proto at 2015-01-20 16:13:00
 *
 * Cntysoft.Framework.Cloud.Ali.Ots.Msg package
 */

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ColumnType enum
 */
final class ColumnType
{
    const INF_MIN = 0;
    const INF_MAX = 1;
    const INTEGER = 2;
    const STRING = 3;
    const BOOLEAN = 4;
    const DOUBLE = 5;
    const BINARY = 6;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'INF_MIN' => self::INF_MIN,
            'INF_MAX' => self::INF_MAX,
            'INTEGER' => self::INTEGER,
            'STRING' => self::STRING,
            'BOOLEAN' => self::BOOLEAN,
            'DOUBLE' => self::DOUBLE,
            'BINARY' => self::BINARY,
        );
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * Direction enum
 */
final class Direction
{
    const FORWARD = 0;
    const BACKWARD = 1;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'FORWARD' => self::FORWARD,
            'BACKWARD' => self::BACKWARD,
        );
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * OperationType enum
 */
final class OperationType
{
    const PUT = 1;
    const DELETE = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'PUT' => self::PUT,
            'DELETE' => self::DELETE,
        );
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * RowExistenceExpectation enum
 */
final class RowExistenceExpectation
{
    const IGNORE = 0;
    const EXPECT_EXIST = 1;
    const EXPECT_NOT_EXIST = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'IGNORE' => self::IGNORE,
            'EXPECT_EXIST' => self::EXPECT_EXIST,
            'EXPECT_NOT_EXIST' => self::EXPECT_NOT_EXIST,
        );
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * CapacityUnit message
 */
class CapacityUnit extends \ProtobufMessage
{
    /* Field index constants */
    const READ = 1;
    const WRITE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::READ => array(
            'name' => 'read',
            'required' => false,
            'type' => 5,
        ),
        self::WRITE => array(
            'name' => 'write',
            'required' => false,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::READ] = null;
        $this->values[self::WRITE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'read' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setRead($value)
    {
        return $this->set(self::READ, $value);
    }

    /**
     * Returns value of 'read' property
     *
     * @return int
     */
    public function getRead()
    {
        return $this->get(self::READ);
    }

    /**
     * Sets value of 'write' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setWrite($value)
    {
        return $this->set(self::WRITE, $value);
    }

    /**
     * Returns value of 'write' property
     *
     * @return int
     */
    public function getWrite()
    {
        return $this->get(self::WRITE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * Column message
 */
class Column extends \ProtobufMessage
{
    /* Field index constants */
    const NAME = 1;
    const VALUE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::NAME => array(
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
        self::VALUE => array(
            'name' => 'value',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::NAME] = null;
        $this->values[self::VALUE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }

    /**
     * Sets value of 'value' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue $value Property value
     *
     * @return null
     */
    public function setValue(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue $value)
    {
        return $this->set(self::VALUE, $value);
    }

    /**
     * Returns value of 'value' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue
     */
    public function getValue()
    {
        return $this->get(self::VALUE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ColumnSchema message
 */
class ColumnSchema extends \ProtobufMessage
{
    /* Field index constants */
    const NAME = 1;
    const TYPE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::NAME => array(
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
        self::TYPE => array(
            'name' => 'type',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::NAME] = null;
        $this->values[self::TYPE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }

    /**
     * Sets value of 'type' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setType($value)
    {
        return $this->set(self::TYPE, $value);
    }

    /**
     * Returns value of 'type' property
     *
     * @return int
     */
    public function getType()
    {
        return $this->get(self::TYPE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ColumnUpdate message
 */
class ColumnUpdate extends \ProtobufMessage
{
    /* Field index constants */
    const TYPE = 1;
    const NAME = 2;
    const VALUE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TYPE => array(
            'name' => 'type',
            'required' => true,
            'type' => 5,
        ),
        self::NAME => array(
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
        self::VALUE => array(
            'name' => 'value',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TYPE] = null;
        $this->values[self::NAME] = null;
        $this->values[self::VALUE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'type' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setType($value)
    {
        return $this->set(self::TYPE, $value);
    }

    /**
     * Returns value of 'type' property
     *
     * @return int
     */
    public function getType()
    {
        return $this->get(self::TYPE);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }

    /**
     * Sets value of 'value' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue $value Property value
     *
     * @return null
     */
    public function setValue(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue $value)
    {
        return $this->set(self::VALUE, $value);
    }

    /**
     * Returns value of 'value' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnValue
     */
    public function getValue()
    {
        return $this->get(self::VALUE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ColumnValue message
 */
class ColumnValue extends \ProtobufMessage
{
    /* Field index constants */
    const TYPE = 1;
    const V_INT = 2;
    const V_STRING = 3;
    const V_BOOL = 4;
    const V_DOUBLE = 5;
    const V_BINARY = 6;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TYPE => array(
            'name' => 'type',
            'required' => true,
            'type' => 5,
        ),
        self::V_INT => array(
            'name' => 'v_int',
            'required' => false,
            'type' => 5,
        ),
        self::V_STRING => array(
            'name' => 'v_string',
            'required' => false,
            'type' => 7,
        ),
        self::V_BOOL => array(
            'name' => 'v_bool',
            'required' => false,
            'type' => 8,
        ),
        self::V_DOUBLE => array(
            'name' => 'v_double',
            'required' => false,
            'type' => 1,
        ),
        self::V_BINARY => array(
            'name' => 'v_binary',
            'required' => false,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TYPE] = null;
        $this->values[self::V_INT] = null;
        $this->values[self::V_STRING] = null;
        $this->values[self::V_BOOL] = null;
        $this->values[self::V_DOUBLE] = null;
        $this->values[self::V_BINARY] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'type' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setType($value)
    {
        return $this->set(self::TYPE, $value);
    }

    /**
     * Returns value of 'type' property
     *
     * @return int
     */
    public function getType()
    {
        return $this->get(self::TYPE);
    }

    /**
     * Sets value of 'v_int' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setVInt($value)
    {
        return $this->set(self::V_INT, $value);
    }

    /**
     * Returns value of 'v_int' property
     *
     * @return int
     */
    public function getVInt()
    {
        return $this->get(self::V_INT);
    }

    /**
     * Sets value of 'v_string' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVString($value)
    {
        return $this->set(self::V_STRING, $value);
    }

    /**
     * Returns value of 'v_string' property
     *
     * @return string
     */
    public function getVString()
    {
        return $this->get(self::V_STRING);
    }

    /**
     * Sets value of 'v_bool' property
     *
     * @param bool $value Property value
     *
     * @return null
     */
    public function setVBool($value)
    {
        return $this->set(self::V_BOOL, $value);
    }

    /**
     * Returns value of 'v_bool' property
     *
     * @return bool
     */
    public function getVBool()
    {
        return $this->get(self::V_BOOL);
    }

    /**
     * Sets value of 'v_double' property
     *
     * @param float $value Property value
     *
     * @return null
     */
    public function setVDouble($value)
    {
        return $this->set(self::V_DOUBLE, $value);
    }

    /**
     * Returns value of 'v_double' property
     *
     * @return float
     */
    public function getVDouble()
    {
        return $this->get(self::V_DOUBLE);
    }

    /**
     * Sets value of 'v_binary' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVBinary($value)
    {
        return $this->set(self::V_BINARY, $value);
    }

    /**
     * Returns value of 'v_binary' property
     *
     * @return string
     */
    public function getVBinary()
    {
        return $this->get(self::V_BINARY);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * Condition message
 */
class Condition extends \ProtobufMessage
{
    /* Field index constants */
    const ROW_EXISTENCE = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::ROW_EXISTENCE => array(
            'name' => 'row_existence',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::ROW_EXISTENCE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'row_existence' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setRowExistence($value)
    {
        return $this->set(self::ROW_EXISTENCE, $value);
    }

    /**
     * Returns value of 'row_existence' property
     *
     * @return int
     */
    public function getRowExistence()
    {
        return $this->get(self::ROW_EXISTENCE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ConsumedCapacity message
 */
class ConsumedCapacity extends \ProtobufMessage
{
    /* Field index constants */
    const CAPACITY_UNIT = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CAPACITY_UNIT => array(
            'name' => 'capacity_unit',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CAPACITY_UNIT] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'capacity_unit' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value Property value
     *
     * @return null
     */
    public function setCapacityUnit(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value)
    {
        return $this->set(self::CAPACITY_UNIT, $value);
    }

    /**
     * Returns value of 'capacity_unit' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit
     */
    public function getCapacityUnit()
    {
        return $this->get(self::CAPACITY_UNIT);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * DeleteRowInBatchWriteRowRequest message
 */
class DeleteRowInBatchWriteRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const CONDITION = 1;
    const PRIMARY_KEY = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CONDITION => array(
            'name' => 'condition',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition'
        ),
        self::PRIMARY_KEY => array(
            'name' => 'primary_key',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CONDITION] = null;
        $this->values[self::PRIMARY_KEY] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'condition' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value Property value
     *
     * @return null
     */
    public function setCondition(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value)
    {
        return $this->set(self::CONDITION, $value);
    }

    /**
     * Returns value of 'condition' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition
     */
    public function getCondition()
    {
        return $this->get(self::CONDITION);
    }

    /**
     * Appends value to 'primary_key' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKey(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::PRIMARY_KEY, $value);
    }

    /**
     * Clears 'primary_key' list
     *
     * @return null
     */
    public function clearPrimaryKey()
    {
        return $this->clear(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getPrimaryKey()
    {
        return $this->get(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY));
    }

    /**
     * Returns element from 'primary_key' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getPrimaryKeyAt($offset)
    {
        return $this->get(self::PRIMARY_KEY, $offset);
    }

    /**
     * Returns count of 'primary_key' list
     *
     * @return int
     */
    public function getPrimaryKeyCount()
    {
        return $this->count(self::PRIMARY_KEY);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * Error message
 */
class Error extends \ProtobufMessage
{
    /* Field index constants */
    const CODE = 1;
    const MESSAGE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CODE => array(
            'name' => 'code',
            'required' => true,
            'type' => 7,
        ),
        self::MESSAGE => array(
            'name' => 'message',
            'required' => false,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CODE] = null;
        $this->values[self::MESSAGE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'code' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return string
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }

    /**
     * Sets value of 'message' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setMessage($value)
    {
        return $this->set(self::MESSAGE, $value);
    }

    /**
     * Returns value of 'message' property
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->get(self::MESSAGE);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * PutRowInBatchWriteRowRequest message
 */
class PutRowInBatchWriteRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const CONDITION = 1;
    const PRIMARY_KEY = 2;
    const ATTRIBUTE_COLUMNS = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CONDITION => array(
            'name' => 'condition',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition'
        ),
        self::PRIMARY_KEY => array(
            'name' => 'primary_key',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
        self::ATTRIBUTE_COLUMNS => array(
            'name' => 'attribute_columns',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CONDITION] = null;
        $this->values[self::PRIMARY_KEY] = array();
        $this->values[self::ATTRIBUTE_COLUMNS] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'condition' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value Property value
     *
     * @return null
     */
    public function setCondition(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value)
    {
        return $this->set(self::CONDITION, $value);
    }

    /**
     * Returns value of 'condition' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition
     */
    public function getCondition()
    {
        return $this->get(self::CONDITION);
    }

    /**
     * Appends value to 'primary_key' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKey(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::PRIMARY_KEY, $value);
    }

    /**
     * Clears 'primary_key' list
     *
     * @return null
     */
    public function clearPrimaryKey()
    {
        return $this->clear(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getPrimaryKey()
    {
        return $this->get(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY));
    }

    /**
     * Returns element from 'primary_key' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getPrimaryKeyAt($offset)
    {
        return $this->get(self::PRIMARY_KEY, $offset);
    }

    /**
     * Returns count of 'primary_key' list
     *
     * @return int
     */
    public function getPrimaryKeyCount()
    {
        return $this->count(self::PRIMARY_KEY);
    }

    /**
     * Appends value to 'attribute_columns' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendAttributeColumns(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::ATTRIBUTE_COLUMNS, $value);
    }

    /**
     * Clears 'attribute_columns' list
     *
     * @return null
     */
    public function clearAttributeColumns()
    {
        return $this->clear(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getAttributeColumns()
    {
        return $this->get(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' iterator
     *
     * @return ArrayIterator
     */
    public function getAttributeColumnsIterator()
    {
        return new \ArrayIterator($this->get(self::ATTRIBUTE_COLUMNS));
    }

    /**
     * Returns element from 'attribute_columns' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getAttributeColumnsAt($offset)
    {
        return $this->get(self::ATTRIBUTE_COLUMNS, $offset);
    }

    /**
     * Returns count of 'attribute_columns' list
     *
     * @return int
     */
    public function getAttributeColumnsCount()
    {
        return $this->count(self::ATTRIBUTE_COLUMNS);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ReservedThroughput message
 */
class ReservedThroughput extends \ProtobufMessage
{
    /* Field index constants */
    const CAPACITY_UNIT = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CAPACITY_UNIT => array(
            'name' => 'capacity_unit',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CAPACITY_UNIT] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'capacity_unit' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value Property value
     *
     * @return null
     */
    public function setCapacityUnit(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value)
    {
        return $this->set(self::CAPACITY_UNIT, $value);
    }

    /**
     * Returns value of 'capacity_unit' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit
     */
    public function getCapacityUnit()
    {
        return $this->get(self::CAPACITY_UNIT);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * ReservedThroughputDetails message
 */
class ReservedThroughputDetails extends \ProtobufMessage
{
    /* Field index constants */
    const CAPACITY_UNIT = 1;
    const LAST_INCREASE_TIME = 2;
    const LAST_DECREASE_TIME = 3;
    const NUMBER_OF_DECREASES_TODAY = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CAPACITY_UNIT => array(
            'name' => 'capacity_unit',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit'
        ),
        self::LAST_INCREASE_TIME => array(
            'name' => 'last_increase_time',
            'required' => true,
            'type' => 5,
        ),
        self::LAST_DECREASE_TIME => array(
            'name' => 'last_decrease_time',
            'required' => false,
            'type' => 5,
        ),
        self::NUMBER_OF_DECREASES_TODAY => array(
            'name' => 'number_of_decreases_today',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CAPACITY_UNIT] = null;
        $this->values[self::LAST_INCREASE_TIME] = null;
        $this->values[self::LAST_DECREASE_TIME] = null;
        $this->values[self::NUMBER_OF_DECREASES_TODAY] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'capacity_unit' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value Property value
     *
     * @return null
     */
    public function setCapacityUnit(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit $value)
    {
        return $this->set(self::CAPACITY_UNIT, $value);
    }

    /**
     * Returns value of 'capacity_unit' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\CapacityUnit
     */
    public function getCapacityUnit()
    {
        return $this->get(self::CAPACITY_UNIT);
    }

    /**
     * Sets value of 'last_increase_time' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setLastIncreaseTime($value)
    {
        return $this->set(self::LAST_INCREASE_TIME, $value);
    }

    /**
     * Returns value of 'last_increase_time' property
     *
     * @return int
     */
    public function getLastIncreaseTime()
    {
        return $this->get(self::LAST_INCREASE_TIME);
    }

    /**
     * Sets value of 'last_decrease_time' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setLastDecreaseTime($value)
    {
        return $this->set(self::LAST_DECREASE_TIME, $value);
    }

    /**
     * Returns value of 'last_decrease_time' property
     *
     * @return int
     */
    public function getLastDecreaseTime()
    {
        return $this->get(self::LAST_DECREASE_TIME);
    }

    /**
     * Sets value of 'number_of_decreases_today' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNumberOfDecreasesToday($value)
    {
        return $this->set(self::NUMBER_OF_DECREASES_TODAY, $value);
    }

    /**
     * Returns value of 'number_of_decreases_today' property
     *
     * @return int
     */
    public function getNumberOfDecreasesToday()
    {
        return $this->get(self::NUMBER_OF_DECREASES_TODAY);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * Row message
 */
class Row extends \ProtobufMessage
{
    /* Field index constants */
    const PRIMARY_KEY_COLUMNS = 1;
    const ATTRIBUTE_COLUMNS = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::PRIMARY_KEY_COLUMNS => array(
            'name' => 'primary_key_columns',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
        self::ATTRIBUTE_COLUMNS => array(
            'name' => 'attribute_columns',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::PRIMARY_KEY_COLUMNS] = array();
        $this->values[self::ATTRIBUTE_COLUMNS] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Appends value to 'primary_key_columns' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKeyColumns(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::PRIMARY_KEY_COLUMNS, $value);
    }

    /**
     * Clears 'primary_key_columns' list
     *
     * @return null
     */
    public function clearPrimaryKeyColumns()
    {
        return $this->clear(self::PRIMARY_KEY_COLUMNS);
    }

    /**
     * Returns 'primary_key_columns' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getPrimaryKeyColumns()
    {
        return $this->get(self::PRIMARY_KEY_COLUMNS);
    }

    /**
     * Returns 'primary_key_columns' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyColumnsIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY_COLUMNS));
    }

    /**
     * Returns element from 'primary_key_columns' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getPrimaryKeyColumnsAt($offset)
    {
        return $this->get(self::PRIMARY_KEY_COLUMNS, $offset);
    }

    /**
     * Returns count of 'primary_key_columns' list
     *
     * @return int
     */
    public function getPrimaryKeyColumnsCount()
    {
        return $this->count(self::PRIMARY_KEY_COLUMNS);
    }

    /**
     * Appends value to 'attribute_columns' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendAttributeColumns(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::ATTRIBUTE_COLUMNS, $value);
    }

    /**
     * Clears 'attribute_columns' list
     *
     * @return null
     */
    public function clearAttributeColumns()
    {
        return $this->clear(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getAttributeColumns()
    {
        return $this->get(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' iterator
     *
     * @return ArrayIterator
     */
    public function getAttributeColumnsIterator()
    {
        return new \ArrayIterator($this->get(self::ATTRIBUTE_COLUMNS));
    }

    /**
     * Returns element from 'attribute_columns' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getAttributeColumnsAt($offset)
    {
        return $this->get(self::ATTRIBUTE_COLUMNS, $offset);
    }

    /**
     * Returns count of 'attribute_columns' list
     *
     * @return int
     */
    public function getAttributeColumnsCount()
    {
        return $this->count(self::ATTRIBUTE_COLUMNS);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * RowInBatchGetRowRequest message
 */
class RowInBatchGetRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const PRIMARY_KEY = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::PRIMARY_KEY => array(
            'name' => 'primary_key',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::PRIMARY_KEY] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Appends value to 'primary_key' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKey(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::PRIMARY_KEY, $value);
    }

    /**
     * Clears 'primary_key' list
     *
     * @return null
     */
    public function clearPrimaryKey()
    {
        return $this->clear(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getPrimaryKey()
    {
        return $this->get(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY));
    }

    /**
     * Returns element from 'primary_key' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getPrimaryKeyAt($offset)
    {
        return $this->get(self::PRIMARY_KEY, $offset);
    }

    /**
     * Returns count of 'primary_key' list
     *
     * @return int
     */
    public function getPrimaryKeyCount()
    {
        return $this->count(self::PRIMARY_KEY);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * RowInBatchGetRowResponse message
 */
class RowInBatchGetRowResponse extends \ProtobufMessage
{
    /* Field index constants */
    const IS_OK = 1;
    const ERROR = 2;
    const CONSUMED = 3;
    const ROW = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::IS_OK => array(
            'default' => true, 
            'name' => 'is_ok',
            'required' => true,
            'type' => 8,
        ),
        self::ERROR => array(
            'name' => 'error',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error'
        ),
        self::CONSUMED => array(
            'name' => 'consumed',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity'
        ),
        self::ROW => array(
            'name' => 'row',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Row'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::IS_OK] = null;
        $this->values[self::ERROR] = null;
        $this->values[self::CONSUMED] = null;
        $this->values[self::ROW] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'is_ok' property
     *
     * @param bool $value Property value
     *
     * @return null
     */
    public function setIsOk($value)
    {
        return $this->set(self::IS_OK, $value);
    }

    /**
     * Returns value of 'is_ok' property
     *
     * @return bool
     */
    public function getIsOk()
    {
        return $this->get(self::IS_OK);
    }

    /**
     * Sets value of 'error' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error $value Property value
     *
     * @return null
     */
    public function setError(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error $value)
    {
        return $this->set(self::ERROR, $value);
    }

    /**
     * Returns value of 'error' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error
     */
    public function getError()
    {
        return $this->get(self::ERROR);
    }

    /**
     * Sets value of 'consumed' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity $value Property value
     *
     * @return null
     */
    public function setConsumed(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity $value)
    {
        return $this->set(self::CONSUMED, $value);
    }

    /**
     * Returns value of 'consumed' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity
     */
    public function getConsumed()
    {
        return $this->get(self::CONSUMED);
    }

    /**
     * Sets value of 'row' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Row $value Property value
     *
     * @return null
     */
    public function setRow(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Row $value)
    {
        return $this->set(self::ROW, $value);
    }

    /**
     * Returns value of 'row' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Row
     */
    public function getRow()
    {
        return $this->get(self::ROW);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * RowInBatchWriteRowResponse message
 */
class RowInBatchWriteRowResponse extends \ProtobufMessage
{
    /* Field index constants */
    const IS_OK = 1;
    const ERROR = 2;
    const CONSUMED = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::IS_OK => array(
            'default' => true, 
            'name' => 'is_ok',
            'required' => true,
            'type' => 8,
        ),
        self::ERROR => array(
            'name' => 'error',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error'
        ),
        self::CONSUMED => array(
            'name' => 'consumed',
            'required' => false,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::IS_OK] = null;
        $this->values[self::ERROR] = null;
        $this->values[self::CONSUMED] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'is_ok' property
     *
     * @param bool $value Property value
     *
     * @return null
     */
    public function setIsOk($value)
    {
        return $this->set(self::IS_OK, $value);
    }

    /**
     * Returns value of 'is_ok' property
     *
     * @return bool
     */
    public function getIsOk()
    {
        return $this->get(self::IS_OK);
    }

    /**
     * Sets value of 'error' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error $value Property value
     *
     * @return null
     */
    public function setError(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error $value)
    {
        return $this->set(self::ERROR, $value);
    }

    /**
     * Returns value of 'error' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Error
     */
    public function getError()
    {
        return $this->get(self::ERROR);
    }

    /**
     * Sets value of 'consumed' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity $value Property value
     *
     * @return null
     */
    public function setConsumed(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity $value)
    {
        return $this->set(self::CONSUMED, $value);
    }

    /**
     * Returns value of 'consumed' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ConsumedCapacity
     */
    public function getConsumed()
    {
        return $this->get(self::CONSUMED);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * TableInBatchGetRowRequest message
 */
class TableInBatchGetRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_NAME = 1;
    const ROWS = 2;
    const COLUMNS_TO_GET = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_NAME => array(
            'name' => 'table_name',
            'required' => true,
            'type' => 7,
        ),
        self::ROWS => array(
            'name' => 'rows',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowRequest'
        ),
        self::COLUMNS_TO_GET => array(
            'name' => 'columns_to_get',
            'repeated' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_NAME] = null;
        $this->values[self::ROWS] = array();
        $this->values[self::COLUMNS_TO_GET] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTableName($value)
    {
        return $this->set(self::TABLE_NAME, $value);
    }

    /**
     * Returns value of 'table_name' property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->get(self::TABLE_NAME);
    }

    /**
     * Appends value to 'rows' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowRequest $value Value to append
     *
     * @return null
     */
    public function appendRows(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowRequest $value)
    {
        return $this->append(self::ROWS, $value);
    }

    /**
     * Clears 'rows' list
     *
     * @return null
     */
    public function clearRows()
    {
        return $this->clear(self::ROWS);
    }

    /**
     * Returns 'rows' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowRequest[]
     */
    public function getRows()
    {
        return $this->get(self::ROWS);
    }

    /**
     * Returns 'rows' iterator
     *
     * @return ArrayIterator
     */
    public function getRowsIterator()
    {
        return new \ArrayIterator($this->get(self::ROWS));
    }

    /**
     * Returns element from 'rows' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowRequest
     */
    public function getRowsAt($offset)
    {
        return $this->get(self::ROWS, $offset);
    }

    /**
     * Returns count of 'rows' list
     *
     * @return int
     */
    public function getRowsCount()
    {
        return $this->count(self::ROWS);
    }

    /**
     * Appends value to 'columns_to_get' list
     *
     * @param string $value Value to append
     *
     * @return null
     */
    public function appendColumnsToGet($value)
    {
        return $this->append(self::COLUMNS_TO_GET, $value);
    }

    /**
     * Clears 'columns_to_get' list
     *
     * @return null
     */
    public function clearColumnsToGet()
    {
        return $this->clear(self::COLUMNS_TO_GET);
    }

    /**
     * Returns 'columns_to_get' list
     *
     * @return string[]
     */
    public function getColumnsToGet()
    {
        return $this->get(self::COLUMNS_TO_GET);
    }

    /**
     * Returns 'columns_to_get' iterator
     *
     * @return ArrayIterator
     */
    public function getColumnsToGetIterator()
    {
        return new \ArrayIterator($this->get(self::COLUMNS_TO_GET));
    }

    /**
     * Returns element from 'columns_to_get' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return string
     */
    public function getColumnsToGetAt($offset)
    {
        return $this->get(self::COLUMNS_TO_GET, $offset);
    }

    /**
     * Returns count of 'columns_to_get' list
     *
     * @return int
     */
    public function getColumnsToGetCount()
    {
        return $this->count(self::COLUMNS_TO_GET);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * TableInBatchGetRowResponse message
 */
class TableInBatchGetRowResponse extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_NAME = 1;
    const ROWS = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_NAME => array(
            'name' => 'table_name',
            'required' => true,
            'type' => 7,
        ),
        self::ROWS => array(
            'name' => 'rows',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowResponse'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_NAME] = null;
        $this->values[self::ROWS] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTableName($value)
    {
        return $this->set(self::TABLE_NAME, $value);
    }

    /**
     * Returns value of 'table_name' property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->get(self::TABLE_NAME);
    }

    /**
     * Appends value to 'rows' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowResponse $value Value to append
     *
     * @return null
     */
    public function appendRows(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowResponse $value)
    {
        return $this->append(self::ROWS, $value);
    }

    /**
     * Clears 'rows' list
     *
     * @return null
     */
    public function clearRows()
    {
        return $this->clear(self::ROWS);
    }

    /**
     * Returns 'rows' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowResponse[]
     */
    public function getRows()
    {
        return $this->get(self::ROWS);
    }

    /**
     * Returns 'rows' iterator
     *
     * @return ArrayIterator
     */
    public function getRowsIterator()
    {
        return new \ArrayIterator($this->get(self::ROWS));
    }

    /**
     * Returns element from 'rows' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchGetRowResponse
     */
    public function getRowsAt($offset)
    {
        return $this->get(self::ROWS, $offset);
    }

    /**
     * Returns count of 'rows' list
     *
     * @return int
     */
    public function getRowsCount()
    {
        return $this->count(self::ROWS);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * TableInBatchWriteRowRequest message
 */
class TableInBatchWriteRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_NAME = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_NAME => array(
            'name' => 'table_name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTableName($value)
    {
        return $this->set(self::TABLE_NAME, $value);
    }

    /**
     * Returns value of 'table_name' property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->get(self::TABLE_NAME);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * TableInBatchWriteRowResponse message
 */
class TableInBatchWriteRowResponse extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_NAME = 1;
    const PUT_ROWS = 2;
    const UPDATE_ROWS = 3;
    const DELETE_ROWS = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_NAME => array(
            'name' => 'table_name',
            'required' => true,
            'type' => 7,
        ),
        self::PUT_ROWS => array(
            'name' => 'put_rows',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse'
        ),
        self::UPDATE_ROWS => array(
            'name' => 'update_rows',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse'
        ),
        self::DELETE_ROWS => array(
            'name' => 'delete_rows',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_NAME] = null;
        $this->values[self::PUT_ROWS] = array();
        $this->values[self::UPDATE_ROWS] = array();
        $this->values[self::DELETE_ROWS] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTableName($value)
    {
        return $this->set(self::TABLE_NAME, $value);
    }

    /**
     * Returns value of 'table_name' property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->get(self::TABLE_NAME);
    }

    /**
     * Appends value to 'put_rows' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value Value to append
     *
     * @return null
     */
    public function appendPutRows(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value)
    {
        return $this->append(self::PUT_ROWS, $value);
    }

    /**
     * Clears 'put_rows' list
     *
     * @return null
     */
    public function clearPutRows()
    {
        return $this->clear(self::PUT_ROWS);
    }

    /**
     * Returns 'put_rows' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse[]
     */
    public function getPutRows()
    {
        return $this->get(self::PUT_ROWS);
    }

    /**
     * Returns 'put_rows' iterator
     *
     * @return ArrayIterator
     */
    public function getPutRowsIterator()
    {
        return new \ArrayIterator($this->get(self::PUT_ROWS));
    }

    /**
     * Returns element from 'put_rows' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse
     */
    public function getPutRowsAt($offset)
    {
        return $this->get(self::PUT_ROWS, $offset);
    }

    /**
     * Returns count of 'put_rows' list
     *
     * @return int
     */
    public function getPutRowsCount()
    {
        return $this->count(self::PUT_ROWS);
    }

    /**
     * Appends value to 'update_rows' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value Value to append
     *
     * @return null
     */
    public function appendUpdateRows(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value)
    {
        return $this->append(self::UPDATE_ROWS, $value);
    }

    /**
     * Clears 'update_rows' list
     *
     * @return null
     */
    public function clearUpdateRows()
    {
        return $this->clear(self::UPDATE_ROWS);
    }

    /**
     * Returns 'update_rows' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse[]
     */
    public function getUpdateRows()
    {
        return $this->get(self::UPDATE_ROWS);
    }

    /**
     * Returns 'update_rows' iterator
     *
     * @return ArrayIterator
     */
    public function getUpdateRowsIterator()
    {
        return new \ArrayIterator($this->get(self::UPDATE_ROWS));
    }

    /**
     * Returns element from 'update_rows' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse
     */
    public function getUpdateRowsAt($offset)
    {
        return $this->get(self::UPDATE_ROWS, $offset);
    }

    /**
     * Returns count of 'update_rows' list
     *
     * @return int
     */
    public function getUpdateRowsCount()
    {
        return $this->count(self::UPDATE_ROWS);
    }

    /**
     * Appends value to 'delete_rows' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value Value to append
     *
     * @return null
     */
    public function appendDeleteRows(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse $value)
    {
        return $this->append(self::DELETE_ROWS, $value);
    }

    /**
     * Clears 'delete_rows' list
     *
     * @return null
     */
    public function clearDeleteRows()
    {
        return $this->clear(self::DELETE_ROWS);
    }

    /**
     * Returns 'delete_rows' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse[]
     */
    public function getDeleteRows()
    {
        return $this->get(self::DELETE_ROWS);
    }

    /**
     * Returns 'delete_rows' iterator
     *
     * @return ArrayIterator
     */
    public function getDeleteRowsIterator()
    {
        return new \ArrayIterator($this->get(self::DELETE_ROWS));
    }

    /**
     * Returns element from 'delete_rows' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\RowInBatchWriteRowResponse
     */
    public function getDeleteRowsAt($offset)
    {
        return $this->get(self::DELETE_ROWS, $offset);
    }

    /**
     * Returns count of 'delete_rows' list
     *
     * @return int
     */
    public function getDeleteRowsCount()
    {
        return $this->count(self::DELETE_ROWS);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * TableMeta message
 */
class TableMeta extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_NAME = 1;
    const PRIMARY_KEY = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_NAME => array(
            'name' => 'table_name',
            'required' => true,
            'type' => 7,
        ),
        self::PRIMARY_KEY => array(
            'name' => 'primary_key',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnSchema'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_NAME] = null;
        $this->values[self::PRIMARY_KEY] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTableName($value)
    {
        return $this->set(self::TABLE_NAME, $value);
    }

    /**
     * Returns value of 'table_name' property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->get(self::TABLE_NAME);
    }

    /**
     * Appends value to 'primary_key' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnSchema $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKey(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnSchema $value)
    {
        return $this->append(self::PRIMARY_KEY, $value);
    }

    /**
     * Clears 'primary_key' list
     *
     * @return null
     */
    public function clearPrimaryKey()
    {
        return $this->clear(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnSchema[]
     */
    public function getPrimaryKey()
    {
        return $this->get(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY));
    }

    /**
     * Returns element from 'primary_key' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnSchema
     */
    public function getPrimaryKeyAt($offset)
    {
        return $this->get(self::PRIMARY_KEY, $offset);
    }

    /**
     * Returns count of 'primary_key' list
     *
     * @return int
     */
    public function getPrimaryKeyCount()
    {
        return $this->count(self::PRIMARY_KEY);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * UpdateRowInBatchWriteRowRequest message
 */
class UpdateRowInBatchWriteRowRequest extends \ProtobufMessage
{
    /* Field index constants */
    const CONDITION = 1;
    const PRIMARY_KEY = 2;
    const ATTRIBUTE_COLUMNS = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CONDITION => array(
            'name' => 'condition',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition'
        ),
        self::PRIMARY_KEY => array(
            'name' => 'primary_key',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column'
        ),
        self::ATTRIBUTE_COLUMNS => array(
            'name' => 'attribute_columns',
            'repeated' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnUpdate'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CONDITION] = null;
        $this->values[self::PRIMARY_KEY] = array();
        $this->values[self::ATTRIBUTE_COLUMNS] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'condition' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value Property value
     *
     * @return null
     */
    public function setCondition(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition $value)
    {
        return $this->set(self::CONDITION, $value);
    }

    /**
     * Returns value of 'condition' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Condition
     */
    public function getCondition()
    {
        return $this->get(self::CONDITION);
    }

    /**
     * Appends value to 'primary_key' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value Value to append
     *
     * @return null
     */
    public function appendPrimaryKey(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column $value)
    {
        return $this->append(self::PRIMARY_KEY, $value);
    }

    /**
     * Clears 'primary_key' list
     *
     * @return null
     */
    public function clearPrimaryKey()
    {
        return $this->clear(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column[]
     */
    public function getPrimaryKey()
    {
        return $this->get(self::PRIMARY_KEY);
    }

    /**
     * Returns 'primary_key' iterator
     *
     * @return ArrayIterator
     */
    public function getPrimaryKeyIterator()
    {
        return new \ArrayIterator($this->get(self::PRIMARY_KEY));
    }

    /**
     * Returns element from 'primary_key' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\Column
     */
    public function getPrimaryKeyAt($offset)
    {
        return $this->get(self::PRIMARY_KEY, $offset);
    }

    /**
     * Returns count of 'primary_key' list
     *
     * @return int
     */
    public function getPrimaryKeyCount()
    {
        return $this->count(self::PRIMARY_KEY);
    }

    /**
     * Appends value to 'attribute_columns' list
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnUpdate $value Value to append
     *
     * @return null
     */
    public function appendAttributeColumns(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnUpdate $value)
    {
        return $this->append(self::ATTRIBUTE_COLUMNS, $value);
    }

    /**
     * Clears 'attribute_columns' list
     *
     * @return null
     */
    public function clearAttributeColumns()
    {
        return $this->clear(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnUpdate[]
     */
    public function getAttributeColumns()
    {
        return $this->get(self::ATTRIBUTE_COLUMNS);
    }

    /**
     * Returns 'attribute_columns' iterator
     *
     * @return ArrayIterator
     */
    public function getAttributeColumnsIterator()
    {
        return new \ArrayIterator($this->get(self::ATTRIBUTE_COLUMNS));
    }

    /**
     * Returns element from 'attribute_columns' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ColumnUpdate
     */
    public function getAttributeColumnsAt($offset)
    {
        return $this->get(self::ATTRIBUTE_COLUMNS, $offset);
    }

    /**
     * Returns count of 'attribute_columns' list
     *
     * @return int
     */
    public function getAttributeColumnsCount()
    {
        return $this->count(self::ATTRIBUTE_COLUMNS);
    }
}
}

namespace Cntysoft\Framework\Cloud\Ali\Ots\Msg {
/**
 * CreateTableRequest message
 */
class CreateTableRequest extends \ProtobufMessage
{
    /* Field index constants */
    const TABLE_META = 1;
    const RESERVED_THROUGHPUT = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TABLE_META => array(
            'name' => 'table_meta',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\TableMeta'
        ),
        self::RESERVED_THROUGHPUT => array(
            'name' => 'reserved_throughput',
            'required' => true,
            'type' => '\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ReservedThroughput'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::TABLE_META] = null;
        $this->values[self::RESERVED_THROUGHPUT] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'table_meta' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\TableMeta $value Property value
     *
     * @return null
     */
    public function setTableMeta(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\TableMeta $value)
    {
        return $this->set(self::TABLE_META, $value);
    }

    /**
     * Returns value of 'table_meta' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\TableMeta
     */
    public function getTableMeta()
    {
        return $this->get(self::TABLE_META);
    }

    /**
     * Sets value of 'reserved_throughput' property
     *
     * @param \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ReservedThroughput $value Property value
     *
     * @return null
     */
    public function setReservedThroughput(\Cntysoft\Framework\Cloud\Ali\Ots\Msg\ReservedThroughput $value)
    {
        return $this->set(self::RESERVED_THROUGHPUT, $value);
    }

    /**
     * Returns value of 'reserved_throughput' property
     *
     * @return \Cntysoft\Framework\Cloud\Ali\Ots\Msg\ReservedThroughput
     */
    public function getReservedThroughput()
    {
        return $this->get(self::RESERVED_THROUGHPUT);
    }
}
}
