<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class Node
{
    /**
     * the key of the node, this might seem reduntant,
     * but without this duplication, we don't have a fast way
     * to retrieve the key of a node when we wan't to remove it
     * from the hashmap.
     */
    private $key;

    // the content of the node
    private $data;

    // the next node
    private $next;

    // the previous node
    private $previous;

    /**
     * @param string $key the key of the node
     * @param string $data the content of the node
     */
    public function __construct($key, $data)
    {
        $this->key = $key;
        $this->data = $data;
    }

    /**
     * Sets a new value for the node data.
     * @param string the new content of the node
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Sets a node as the next node.
     * @param Node $next the next node
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    /**
     * Sets a node as the previous node.
     * @param Node $previous the previous node
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;
    }

    /**
     * Returns the node key.
     * @return string the key of the node
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the node data.
     * @return mixed the content of the node
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the next node.
     * @return Node the next node of the node
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Returns the previous node.
     * @return Node the previous node of the node
     */
    public function getPrevious()
    {
        return $this->previous;
    }
}
