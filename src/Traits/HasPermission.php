<?php

namespace Betterde\Authorization\Traits;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Betterde\Authorization\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermission
{
    /**
     * 获取用户特殊权限
     *
     * Date: 19/04/2018
     * @author George
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('permission.model'), config('authorization.relation.user_permission'), 'user_id', 'permission_code', 'id', 'code');
    }

    /**
     * 判断是否拥有权限
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return bool
     */
    public function hasPermission(string $code)
    {
        $permissions = $this->permissions;
        if (is_array($permissions)) {
            $res = false;
            foreach ($permissions as $v){
                if($v->permission_code==$code){
                    $res = true;
                    break;
                }
            }
            return $res;
        }

        if ($permissions instanceof Collection) {
            return $permissions->contains($code);
        }

        return false;
    }

    /**
     * 修改用户权限
     *
     * Date: 19/04/2018
     * @author George
     * @param array $codes
     * @param bool $detaching
     * @return array
     * @throws AuthorizationException
     */
    public function syncPermission(array $codes, $detaching = true)
    {
        if (is_array($codes)) {
            try {
                $result = $this->permissions()->sync($codes, $detaching);
                Redis::connection(config('authorization.cache.database'))->hdel(config('authorization.cache.prefix') . ':user_permissions', $this->id);

                if (!empty($result)) {
                    Redis::connection(config('authorization.cache.database'))->hset(config('authorization.cache.prefix') . ':user_permissions', $this->id, json_encode($result));
                }

                return $result;
            } catch (Exception $exception) {
                throw new AuthorizationException('更新用户权限失败', 500);
            }
        }

        throw new AuthorizationException('请传入正确的参数', 400);
    }

    /**
     * 获取用户权限
     *
     * Date: 19/04/2018
     * @author George
     * @return \Illuminate\Support\Collection|static
     */
    public function getPermissionsAttribute()
    {
        if (config('authorization.cache.enable')) {
            $permissions = json_decode(Redis::connection(config('authorization.cache.database'))
                ->hget(config('authorization.cache.prefix') . ':user_permissions', $this->id));
            if(empty($permissions)){
                $permissions = DB::table(config('authorization.relation.user_permission'))->select('permission_code')->get()->toArray();
                if (!empty($permissions)) {
                Redis::connection(config('authorization.cache.database'))->hset(config('authorization.cache.prefix') . ':user_permissions', $this->id, json_encode($permissions));
                }
            }
        } else {
            $permissions = DB::table(config('authorization.relation.user_permission'))->select('permission_code')->get()->toArray();
        }
        return $permissions;
    }
}