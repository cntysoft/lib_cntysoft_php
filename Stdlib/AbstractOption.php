<?php
/**
 * Cntysoft OpenEngine
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Stdlib;
use Cntysoft\Kernel;
use Cntysoft\Kernel\StdErrorType;
class AbstractOption
{
    /**
     * Constructor
     *
     * @param  array|Traversable|null $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setFromArray($options);
        }
    }

    /**
     * Set one or more configuration properties
     *
     * @param  array|Traversable|AbstractOptions $options
     * @throws Exception
     * @return AbstractOptions Provides fluent interface
     */
    public function setFromArray($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            Kernel\throw_exception(new Exception(
                StdErrorType::msg('E_ARG_TYPE_ERROR', 'array or Traversable'),
                StdErrorType::code('E_ARG_TYPE_ERROR')));
        }

        foreach ($options as $key => $value) {
            $method = 'set'.ucfirst($key);
            $this->{$method}($value);
        }

        return $this;
    }

}