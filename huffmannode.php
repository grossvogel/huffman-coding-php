<?php
/**
 * 	Node in a huffman tree implementing a huffman coding
 */
class HuffmanNode
{
	private $weight = null;
	private $symbol = null;
	private $leftChild = null;
	private $rightChild = null;
	private $parent = null;
	
	/**
	 *	create a new leaf node
	 */
	public function __construct ($symbol, $weight = null)
	{
		$this->symbol = $symbol;
		$this->weight = $weight;
	}
	
	/**
	 * 	create a new node whose children are the supplied nodes
	 */
	public static function join (HuffmanNode $left, HuffmanNode $right)
	{
		$newParent = new HuffmanNode (null, $left->getWeight () + $right->getWeight ());
		$newParent->leftChild = $left;
		$newParent->rightChild = $right;
		return $newParent;
	}
	
	/**
	 * 	perform a depth-first search to construct a 
	 */
	public function getCodeHash (&$hashTable, $prefix = "")
	{
		if ($this->isLeaf ())
		{
			$hashTable[$this->symbol] = $prefix;
		}
		else
		{
			$this->leftChild->getCodeHash ($hashTable, "{$prefix}0");
			$this->rightChild->getCodeHash ($hashTable, "{$prefix}1");
		}
	}
	
	public function isLeaf ()
	{
		return ($this->symbol !== null);
	}
	
	public function getWeight ()
	{
		return $this->weight;
	}
	
	public function getSymbol ()
	{
		return $this->symbol;
	}
	
	public function getRightChild ()
	{
		return $this->rightChild;
	}
	
	public function getLeftChild ()
	{
		return $this->leftChild;
	}
	
	public function getParent ()
	{
		return $this->parent;
	}

	/**
	 * 	read a node encoded using __toString
	 */
	private static function readEncodedNode (&$str)
	{
		$switch = $str[0];
		$str = substr ($str, 1);
		switch ($switch)
		{
			case '0':
				$left = self::readEncodedNode ($str);
				$right = self::readEncodedNode ($str);
				$parent = new HuffmanNode (null);
				$parent->leftChild = $left;
				$parent->rightChild = $right;
				return $parent;
				break;
			case '1':
			case '2':
				$symbol = ($switch == 1) 
					? $str[0] : 
					HuffmanCoding::SYMBOL_EOF;
				$str = substr ($str, 1);
				return new HuffmanNode ($symbol);
				break;
			default:
				throw new Exception ("Encoding is out of sync.");
		}
	}
	
	/**
	 * 	opposite of __toString
	 **/
	public static function loadFromString (&$str)
	{
		return self::readEncodedNode ($str);
	}
	
	/**
	 * 	create a string for compactly encoding this node (and its children)
	 */
	public function __toString ()
	{	
		if ($this->isLeaf ())
		{
			if ($this->symbol === HuffmanCoding::SYMBOL_EOF)
			{
				return '2~';	//	~ stands in BC HuffmanCoding::SYMBOL_EOF may be more than one byte
			}
			else
			{
				return '1' . $this->symbol;
			}
		}
		else
		{
			return '0' . (string) $this->leftChild . (string) $this->rightChild;
		}
	}
}
