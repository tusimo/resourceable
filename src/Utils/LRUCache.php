<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class LRUCache
{
    // object Node representing the head of the list
    private $head;

    // object Node representing the tail of the list
    private $tail;

    // int the max number of elements the cache supports
    private $capacity;

    // Array representing a naive hashmap (TODO needs to pass the key through a hash function)
    private $hashmap;

    /**
     * @param int $capacity the max number of elements the cache allows
     */
    public function __construct($capacity)
    {
        $this->capacity = $capacity;
        $this->hashmap = [];
        $this->head = new Node(null, null);
        $this->tail = new Node(null, null);

        $this->head->setNext($this->tail);
        $this->tail->setPrevious($this->head);
    }

    /**
     * real size of cache.
     *
     * @return int
     */
    public function size()
    {
        return count($this->hashmap);
    }

    /**
     * capacity of cache.
     * @return int
     */
    public function capacity()
    {
        return $this->capacity;
    }

    /**
     * Get an element with the given key.
     * @param string $key the key of the element to be retrieved
     * @param null|mixed $default
     * @return mixed the content of the element to be retrieved
     */
    public function get($key, $default = null)
    {
        if (! isset($this->hashmap[$key])) {
            return $default;
        }

        $node = $this->hashmap[$key];
        if ($this->size() == 1) {
            return $node->getData();
        }

        // refresh the access
        $this->detach($node);
        $this->attach($this->head, $node);

        return $node->getData();
    }

    /**
     * Inserts a new element into the cache.
     * @param string $key the key of the new element
     * @param string $data the content of the new element
     * @return bool true on success, false if cache has zero capacity
     */
    public function put($key, $data)
    {
        if ($this->capacity <= 0) {
            return false;
        }
        if (isset($this->hashmap[$key]) && ! empty($this->hashmap[$key])) {
            $node = $this->hashmap[$key];
            // update data
            $this->detach($node);
            $this->attach($this->head, $node);
            $node->setData($data);
        } else {
            $node = new Node($key, $data);
            $this->hashmap[$key] = $node;
            $this->attach($this->head, $node);

            // check if cache is full
            if ($this->size() > $this->capacity) {
                // we're full, remove the tail
                $nodeToRemove = $this->tail->getPrevious();
                $this->detach($nodeToRemove);
                unset($this->hashmap[$nodeToRemove->getKey()]);
            }
        }
        return true;
    }

    /**
     * Removes a key from the cache.
     * @param string $key key to remove
     * @return bool true if removed, false if not found
     */
    public function remove($key)
    {
        if (! isset($this->hashmap[$key])) {
            return false;
        }
        $nodeToRemove = $this->hashmap[$key];
        $this->detach($nodeToRemove);
        unset($this->hashmap[$nodeToRemove->getKey()]);
        return true;
    }

    /**
     * Adds a node to the head of the list.
     * @param Node $head the node object that represents the head of the list
     * @param Node $node the node to move to the head of the list
     */
    private function attach($head, $node)
    {
        $node->setPrevious($head);
        $node->setNext($head->getNext());
        $node->getNext()->setPrevious($node);
        $node->getPrevious()->setNext($node);
    }

    /**
     * Removes a node from the list.
     * @param Node $node the node to remove from the list
     */
    private function detach($node)
    {
        $node->getPrevious()->setNext($node->getNext());
        $node->getNext()->setPrevious($node->getPrevious());
    }
}
