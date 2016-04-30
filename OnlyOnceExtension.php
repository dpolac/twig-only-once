<?php

namespace DPolac\OnlyOnce;

/**
 * Twig extension which provides tests returning true only once for every value.
 *
 * @author Damian Polac <damian.polac.111@gmail.com>
 */
class OnlyOnceExtension extends \Twig_Extension
{
    /**
     * Two-dimensional hash.
     * occurrencesCounter[space][valueHash]
     *
     * @var string[][]
     */
    private $occurrencesCounter = array();

    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('onlyOnce', array($this, 'onlyOnce')),
            new \Twig_SimpleTest('onlyOnceWhenOccurs', array($this, 'onlyOnceWhenOccurs')),
        );
    }

    /**
     * Returns true only if it's exactly first call with give space-value pair.
     *
     * @param   mixed       $value      Any value
     * @param   string      $space      Name of space
     *
     * @return  bool        true if function is called first time for given space-value pair, false otherwise
     */
    public function onlyOnce($value, $space = 'default')
    {
        if (!is_string($space)) {
            throw new \InvalidArgumentException(
                "Name of space must be a string.");
        }

        return $this->onlyOnceWhenOccurs($value, 1, $space);
    }

    /**
     * Returns true only if it's exactly n-th call with given space-value pair.
     *
     * @param   mixed       $value      Any value
     * @param   int         $n          Number of calls after which function will return true
     * @param   string      $space      Name of space
     *
     * @return  bool        true if function is called n-th time for given space-value pair, false otherwise
     */
    public function onlyOnceWhenOccurs($value, $n, $space = 'default')
    {
        if (!is_int($n) || $n < 1) {
            throw new \InvalidArgumentException(
                "Second argument of onlyOnceWhenOccurs must be a positive integer.");
        }

        if (!is_string($space)) {
            throw new \InvalidArgumentException(
                "Name of space must be a string.");
        }

        $valueHash = $this->getValueHash($value);
        $this->incrementOccurrencesCounter($space, $valueHash);
        return $this->getOccurrencesCounter($space, $valueHash) === $n;
    }

    /**
     * Returns unique hash for each value.
     *
     * @param   $value
     *
     * @return  string  String containing hash for value.
     */
    protected function getValueHash(&$value)
    {
        if (is_scalar($value)) {
            return (string) $value;
        } elseif (is_object($value)) {
            return spl_object_hash($value);
        } else {
            return md5(serialize($this->prepareArrayForHashing($value)));
        }
    }

    /**
     * Replace all objects in array with its spl_object_hash.
     * Sort array by keys. Works recursively.
     *
     * @param array $array
     * @return array
     */
    private function prepareArrayForHashing(array $array)
    {
        ksort($array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->prepareArrayForHashing($value);
            } elseif (is_object($value)) {
                $value = spl_object_hash($value);
            }
        }
        return $array;
   }

    /**
     * Increments occurrence counter by 1.
     *
     * @param string    $space      Name of space.
     * @param string    $valueHash  Value hash.
     */
    private function incrementOccurrencesCounter($space, $valueHash)
    {
        if (!isset($this->occurrencesCounter[$space])) {
            $this->occurrencesCounter[$space] = array();
        }

        if (!isset($this->occurrencesCounter[$space][$valueHash])) {
            $this->occurrencesCounter[$space][$valueHash] = 0;
        }

        $this->occurrencesCounter[$space][$valueHash]++;
    }

    /**
     * Returns counter for given space-valueHash pair.
     *
     * @param string    $space      Name of space.
     * @param string    $valueHash  Value hash.
     *
     * @return  int     Counter for given space-valueHash pair.
     */
    private function getOccurrencesCounter($space, $valueHash)
    {
        if (!isset($this->occurrencesCounter[$space])) {
            return 0;
        } elseif (!isset($this->occurrencesCounter[$space][$valueHash])) {
            return 0;
        } else {
            return $this->occurrencesCounter[$space][$valueHash];
        }
    }

    public function getName()
    {
        return 'dpolac_only_once_extension';
    }
}
