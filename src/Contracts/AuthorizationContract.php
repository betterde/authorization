<?php

namespace Betterde\Authorization\Contracts;

interface AuthorizationContract
{
    public function roles();

    public function permissions();

    public function syncRole();

    public function syncPermission();

    public function hasRole();

    public function hasPermission();
}