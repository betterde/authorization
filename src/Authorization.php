<?php

namespace Betterde\Authorization;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Betterde\Authorization\Traits\HasRole;
use Illuminate\Auth\Passwords\CanResetPassword;
use Betterde\Authorization\Traits\HasPermission;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Betterde\Authorization\Contracts\AuthorizationContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * 认证模型
 *
 * Date: 19/04/2018
 * @author George
 * @property integer $id
 * @property $permissions
 * @package Betterde\Authorization
 */
class Authorization extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, AuthorizationContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    use HasRole, HasPermission;
}