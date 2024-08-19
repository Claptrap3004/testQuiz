<?php

// options to change the pointer to actual question
namespace quiz;

enum SetActual
{
    case FIRST;
    case PREVIOUS;
    case NEXT;
    case LAST;
    case NONE;
}
