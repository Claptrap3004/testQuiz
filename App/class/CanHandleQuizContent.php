<?php

namespace quiz;

interface CanHandleQuizContent
{
    function create(array $args): int;

    function findById(int $id): array;

    function findAll(array $filters = []): array;

    function update(array $args): bool;

    function deleteAtId(int $id): bool;

    function createTables(): void;

    function setActual(SetActual $setActual): void;
}
