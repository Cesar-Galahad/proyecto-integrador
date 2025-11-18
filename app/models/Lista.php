<?php
// app/Models/LinkedList.php
require_once 'Node.php';

class LinkedList {
  private $head = null;

  public function insert($data) {
    $node = new Node($data);
    if (!$this->head) {
      $this->head = $node;
    } else {
      $current = $this->head;
      while ($current->next) {
        $current = $current->next;
      }
      $current->next = $node;
    }
  }

  public function toArray() {
    $result = [];
    $current = $this->head;
    while ($current) {
      $result[] = $current->data;
      $current = $current->next;
    }
    return $result;
  }
}