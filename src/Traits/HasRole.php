<?php

namespace Betterde\Authorization\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Betterde\Authorization\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRole
{
    /**
     * 获取用户角色
     *
     * Date: 19/04/2018
     * @author George
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('role.model'), config('authorization.relation.user_role'), 'user_id', 'role_code', 'id', 'code');
    }

    /**
     * 判断是否又有指定的角色
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return bool
     */
    public function hasRole(string $code)
    {
        $roles = $this->roles;
        if (is_array($roles)) {
            return array_has($roles, $code);
        }

        if ($roles instanceof Collection) {
            return $roles->contains($code);
        }

        return false;
    }

    /**
     * 更新用户角色
     *
     * Date: 19/04/2018
     * @author George
     * @param array $codes
     * @param bool $detaching
     * @return array
     * @throws AuthorizationException
     */
    public function syncRole(array $codes, $detaching = true)
    {
        if (is_array($codes)) {
            try {
                $result = $this->roles()->sync($codes, $detaching);

                Redis::connection(config('authorization.cache.database'))->hdel(config('authorization.cache.prefix' . ':user_roles'), $this->id);

                if (!empty($result)) {
                    Redis::connection(config('authorization.cache.database'))->hset(config('authorization.cache.prefix' . ':user_roles'), $this->id, json_encode($result));
                }

                return $result;
            } catch (Exception $exception) {
                throw new AuthorizationException('更新用户权限失败', 500);
            }
        }

        throw new AuthorizationException('请传入正确的参数', 400);
    }

    /**
     * 获取用户角色
     *
     * Date: 19/04/2018
     * @author George
     * @return array|mixed
     */
    public function getRolesAttribute()
    {
        if (config('authorization.cache.enable')) {
            $roles = json_decode(Redis::connection(config('authorization.cache.database'))
                ->hget(config('authorization.cache.prefix') . ':user_roles', $this->id));
        } else {
            $roles = DB::table(config('authorization.relation.user_role'))->select('role_code')->get()->toArray();
            if (! empty($roles) && config('authorization.cache.enable')) {
                Redis::connection(config('authorization.cache.database'))->hset(config('authorization.cache.prefix' . ':user_roles'), $this->id, json_encode($roles));
            }
        }

        return $roles;
    }
}