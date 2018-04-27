<?php

namespace Betterde\Authorization\Contracts;

interface AuthorizationContract
{
    public function roles();

    public function permissions();

    public function syncRole(array $codes, $detaching = true);

    public function syncPermission(array $codes, $detaching = true);

    public function hasRole(string $code);

    public function hasPermission(string $code);
}