<?php

/**
*	A queue of Huffman Nodes that need to be incorporated into a tree
*/
class HuffmanNodeQueue
{
	private $nodes = array ();
	
	/**
	 * put the new node into the queue, keeping it in order by weight
	 * NOTE: schliemel going on here! 
	 */
	public function addNode (HuffmanNode $node)
	{
		if (count($this->nodes) == 0)
		{
			$this->nodes = array ($node);
			return;
		}
		
		$index = 0;
		while (isset ($this->nodes[$index]) && $this->nodes[$index]->getWeight () < $node->getWeight ())
		{
			$index++;
		}
		array_splice ($this->nodes, $index, 0, array ($node));
	}
	
	/**
	 * 	get the two nodes with the lowest weights from the front of the queue
	 */
	public function popTwoNodes ()
	{
		if (count($this->nodes) > 1)
		{
			$first = array_shift ($this->nodes);
			$second = array_shift ($this->nodes);
			return array ($first, $second);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 	once there's only one node left, extract it
	 */
	public function getOnlyNode ()
	{
		if (count ($this->nodes) == 1)
		{
			return $this->nodes[0];
		}
		else
		{
			throw new Exception ("Wrong number of nodes. Only call getOnlyNode when exactly one node exists. Nodes: " . print_r ($this->nodes, true));
		}
	}
}

