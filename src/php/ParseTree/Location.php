<?php

namespace Floip\ParseTree;

/**
 * Holds location data for a node
 * @property-read int $start_offset
 * @property-read int $start_line
 * @property-read int $start_column
 * @property-read int $end_offset
 * @property-read int $end_line
 * @property-read int $end_column
 */
final class Location
{
    private $start_offset = 0;
    private $start_line = 0;
    private $start_column = 0;
    private $end_offset = 0;
    private $end_line = 0;
    private $end_column = 0;

    public function __construct($start_offset, $start_line, $start_column, $end_offset, $end_line, $end_column)
    {
        $this->start_offset = $start_offset;
        $this->start_line = $start_line;
        $this->start_column = $start_column;
        $this->end_offset = $end_offset;
        $this->end_line = $end_line;
        $this->end_column = $end_column;
    }

    public function __get($prop)
    {
        return $this->$prop;
    }
}
