<?php
// Interface to provide different implementations for all the different tables respectively classes that implement
// CRUD functionality in the DB
// parameters are defined quite generally, validation of sent data is done in the implementation of this interface

namespace quiz;

interface CanHandleDB
{
    function create(array $args): int;

    function findById(int $id): array;

    function findAll(array $filters = []): array;

    function update(array $args): bool;

    function deleteAtId(int $id): bool;

}