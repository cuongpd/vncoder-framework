<?php

namespace VnCoder\Helper\GeoIP;

use Exception;

class Reader
{
    private static $DATA_SECTION_SEPARATOR_SIZE = 16;
    private static $METADATA_START_MARKER = "\xAB\xCD\xEFMaxMind.com";
    private static $METADATA_START_MARKER_LENGTH = 14;
    private static $METADATA_MAX_SIZE = 131072; // 128 * 1024 = 128KB

    private $decoder;
    private $fileHandle;
    private $fileSize;
    private $ipV4Start;
    private $metadata;

    public function __construct($database)
    {
        if (func_num_args() !== 1) {
            info('The constructor takes exactly one argument.');
            die;
        }

        if (!is_readable($database)) {
            info('The file '.$database.' does not exist or is not readable.');
            die;
        }
        $this->fileHandle = @fopen($database, 'rb');
        if ($this->fileHandle === false) {
            info('Error opening '.$database);
            die;
        }
        $this->fileSize = @filesize($database);
        if ($this->fileSize === false) {
            info('Error determining the size of '.$database);
            die;
        }

        $start = $this->findMetadataStart($database);
        $metadataDecoder = new Decoder($this->fileHandle, $start);
        list($metadataArray) = $metadataDecoder->decode($start);
        $this->metadata = new Metadata($metadataArray);
        $this->decoder = new Decoder($this->fileHandle, $this->metadata->searchTreeSize + self::$DATA_SECTION_SEPARATOR_SIZE);
    }

    public function get($ipAddress)
    {
        if (func_num_args() !== 1) {
            info('Method takes exactly one argument.');
            return false;
        }

        if (!is_resource($this->fileHandle)) {
            info('Attempt to read from a closed MaxMind DB.');
            return false;
        }

        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            info('The value '.$ipAddress.' is not a valid IP address.');
            return false;
        }

        if ($this->metadata->ipVersion === 4 && strrpos($ipAddress, ':')) {
            info("Error looking up $ipAddress. You attempted to look up an IPv6 address in an IPv4-only database");
            return false;
        }
        $pointer = $this->findAddressInTree($ipAddress);
        if ($pointer === 0) {
            return null;
        }
        return $this->resolveDataPointer($pointer);
    }

    private function findAddressInTree($ipAddress)
    {
        // XXX - could simplify. Done as a byte array to ease porting
        $rawAddress = array_merge(unpack('C*', inet_pton($ipAddress)));

        $bitCount = count($rawAddress) * 8;

        // The first node of the tree is always node 0, at the beginning of the
        // value
        $node = $this->startNode($bitCount);

        for ($i = 0; $i < $bitCount; $i++) {
            if ($node >= $this->metadata->nodeCount) {
                break;
            }
            $tempBit = 0xFF & $rawAddress[$i >> 3];
            $bit = 1 & ($tempBit >> 7 - ($i % 8));

            $node = $this->readNode($node, $bit);
        }
        if ($node === $this->metadata->nodeCount) {
            // Record is empty
            return 0;
        } elseif ($node > $this->metadata->nodeCount) {
            // Record is a data pointer
            return $node;
        }
        throw new Exception('Something bad happened');
    }

    private function startNode($length)
    {
        if ($this->metadata->ipVersion === 6 && $length === 32) {
            return $this->ipV4StartNode();
        }
        return 0;
    }

    private function ipV4StartNode()
    {
        if ($this->metadata->ipVersion === 4) {
            return 0;
        }

        if ($this->ipV4Start) {
            return $this->ipV4Start;
        }
        $node = 0;

        for ($i = 0; $i < 96 && $node < $this->metadata->nodeCount; $i++) {
            $node = $this->readNode($node, 0);
        }
        $this->ipV4Start = $node;

        return $node;
    }

    private function readNode($nodeNumber, $index)
    {
        $baseOffset = $nodeNumber * $this->metadata->nodeByteSize;

        // XXX - probably could condense this.
        switch ($this->metadata->recordSize) {
            case 24:
                $bytes = Util::read($this->fileHandle, $baseOffset + $index * 3, 3);
                list(, $node) = unpack('N', "\x00" . $bytes);

                return $node;
            case 28:
                $middleByte = Util::read($this->fileHandle, $baseOffset + 3, 1);
                list(, $middle) = unpack('C', $middleByte);
                if ($index === 0) {
                    $middle = (0xF0 & $middle) >> 4;
                } else {
                    $middle = 0x0F & $middle;
                }
                $bytes = Util::read($this->fileHandle, $baseOffset + $index * 4, 3);
                list(, $node) = unpack('N', chr($middle) . $bytes);

                return $node;
            case 32:
                $bytes = Util::read($this->fileHandle, $baseOffset + $index * 4, 4);
                list(, $node) = unpack('N', $bytes);

                return $node;
            default:
                throw new Exception('Unknown record size: ' . $this->metadata->recordSize);
        }
    }

    private function resolveDataPointer($pointer)
    {
        $resolved = $pointer - $this->metadata->nodeCount + $this->metadata->searchTreeSize;
        if ($resolved > $this->fileSize) {
            info("The MaxMind DB file's search tree is corrupt");
            return false;
        }

        list($data) = $this->decoder->decode($resolved);

        return $data;
    }

    private function findMetadataStart($filename)
    {
        $handle = $this->fileHandle;
        $fstat = fstat($handle);
        $fileSize = $fstat['size'];
        $marker = self::$METADATA_START_MARKER;
        $markerLength = self::$METADATA_START_MARKER_LENGTH;
        $metadataMaxLengthExcludingMarker
            = min(self::$METADATA_MAX_SIZE, $fileSize) - $markerLength;

        for ($i = 0; $i <= $metadataMaxLengthExcludingMarker; $i++) {
            for ($j = 0; $j < $markerLength; $j++) {
                fseek($handle, $fileSize - $i - $j - 1);
                $matchBit = fgetc($handle);
                if ($matchBit !== $marker[$markerLength - $j - 1]) {
                    continue 2;
                }
            }

            return $fileSize - $i;
        }

        throw new Exception("Error opening database file ($filename). " . 'Is this a valid MaxMind DB file?');
    }

    public function metadata()
    {
        if (func_num_args()) {
            info('Method takes no arguments.');
            return false;
        }

        if (!is_resource($this->fileHandle)) {
            info('Attempt to read from a closed MaxMind DB.');
            return false;
        }

        return $this->metadata;
    }

    public function close()
    {
        if (!is_resource($this->fileHandle)) {
            info('Attempt to close a closed MaxMind DB.');
            return false;
        }
        fclose($this->fileHandle);
        return true;
    }
}
