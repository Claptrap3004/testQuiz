<?php

namespace quiz;

interface CanHandleCSV
{
    function readCSV(string $fileName);
    function writeCSV(string $fileName, array $questionIds);
}