<?php

namespace VnCoder\Helper\GeoIP;

class Metadata
{
    private int $binaryFormatMajorVersion;
    private int $binaryFormatMinorVersion;
    private int $buildEpoch;
    private string $databaseType;
    private array $description;
    public int $ipVersion;
    private array $languages;
    public int $nodeByteSize;
    public int $nodeCount;
    public int $recordSize;
    public int $searchTreeSize;

    public function __construct($metadata)
    {
        $this->binaryFormatMajorVersion = $metadata['binary_format_major_version'] ?? 2;
        $this->binaryFormatMinorVersion = $metadata['binary_format_minor_version'] ?? 0;
        $this->buildEpoch = $metadata['build_epoch'] ?? time();
        $this->databaseType = $metadata['database_type'] ?? 'GeoIP2-Country';
        $this->languages = $metadata['languages'] ?? [];
        $this->description = $metadata['description'] ?? [];
        $this->ipVersion = $metadata['ip_version'] ?? 6;
        $this->nodeCount = $metadata['node_count'] ?? 0;
        $this->recordSize = $metadata['record_size'] ?? 24;
        $this->nodeByteSize = $this->recordSize / 4;
        $this->searchTreeSize = $this->nodeCount * $this->nodeByteSize;
    }

    public function __get($var)
    {
        return $this->$var;
    }
}
