<?php
require_once 'bit-util-php/bitstream.php';
require 'huffmannode.php';
require 'huffmannodequeue.php';

/**
*	Implementation of a Huffman Coding
*	http://en.wikipedia.org/wiki/Huffman_coding
*/
class HuffmanCoding
{
	const SYMBOL_EOF = 'EOF';
	
	/**
	 * 	create a code tree whose weights and symbols come from the sample indicated
	 * 	NOTE: if a character isn't in this sample, it can't be encoded with the generated tree
	 */
	public static function createCodeTree ($sample)
	{
		$weights = array ();
		for ($i = 0; $i < strlen ($sample); $i++)
		{
			if (!isset ($weights[$sample[$i]]))
			{
				$weights[$sample[$i]] = 0;
			}
			$weights[$sample[$i]]++;
		}
		$weights[self::SYMBOL_EOF] = 1;	//	add the EOF marker to the encoding
		
		$queue = new HuffmanNodeQueue ();
		arsort ($weights);
		foreach ($weights as $symbol => $weight)
		{
			$queue->addNode (new HuffmanNode ($symbol, $weight));
		}
		
		while ($nodes = $queue->popTwoNodes ())
		{
			$parentNode = HuffmanNode::join ($nodes[0], $nodes[1]);
			$queue->addNode ($parentNode);
		}
		return $queue->getOnlyNode ();
	}
	
	/**
	 * 	encode the given data using the Huffman tree
	 */
	public static function encode ($data, HuffmanNode $codeTree)
	{
		$codeHash = array ();
		$codeTree->getCodeHash ($codeHash);
		$stream = new BitStreamWriter ();
		for ($i = 0; $i < strlen ($data); $i++)
		{
			$symbol = $data[$i];
			if (isset ($codeHash[$symbol]))
			{
				$stream->writeString ($codeHash[$symbol]);
			}
			else
			{
				throw new Exception ("NOTE: Cannot encode symbol {$symbol}. It was not found in the encoding tree.");
			}
		}
		$stream->writeString ($codeHash[self::SYMBOL_EOF]);
		$encodedTree = (string) $codeTree;
		$encodedData = $stream->getData ();
		return $encodedTree . $encodedData;
	}
	
	/**
	 * 	decode the data using the code tree
	 */
	public static function decode ($data)
	{
		$rootNode = HuffmanNode::loadFromString ($data);
		$currentNode = $rootNode;
		$reader = new BitStreamReader ($data);		
		$decoded = "";
		while (true)
		{
			if ($currentNode->isLeaf ())
			{
				$nextSymbol = $currentNode->getSymbol ();
				if ($nextSymbol === self::SYMBOL_EOF)
				{
					return $decoded;
				}
				else
				{
					$decoded .= $nextSymbol;
					$currentNode = $rootNode;
				}
			}
			else
			{
				$bit = $reader->readBit ();
				if ($bit === null)
				{
					throw new Exception ('Reached the end of the encoded data, but did not find the EOF symbol.');
				}
				else
				{
					$currentNode = $bit 
						? $currentNode->getRightChild ()
						: $currentNode->getLeftChild ();
				}
			}
		}
	}
}

