<?php
namespace Minhbang\User;

use Minhbang\Kit\Extensions\Model;
use Minhbang\Kit\Traits\Model\DatetimeQuery;
use Minhbang\Kit\Traits\Model\SearchQuery;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class User
 *
 * @package Minhbang\User
 * @property integer $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $code
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User adminFirst()
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\Kit\Extensions\Model except($id = null)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\Kit\Extensions\Model whereAttributes($attributes)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\Kit\Extensions\Model findText($column, $text)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User orderCreated($direction = 'desc')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User orderUpdated($direction = 'desc')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User period($start = null, $end = null, $field = 'created_at', $end_if_day = false, $is_month = false)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User today($field = 'created_at')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User yesterday($same_time = false, $field = 'created_at')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User thisWeek($field = 'created_at')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User thisMonth($field = 'created_at')
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User searchKeyword($keyword, $columns = null)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User searchWhere($column, $operator = '=', $fn = null)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User searchWhereIn($column, $fn)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User searchWhereBetween($column, $fn = null)
 * @method static \Illuminate\Database\Query\Builder|\Minhbang\User\User searchWhereInDependent($column, $column_dependent, $fn, $empty = [])
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    use DatetimeQuery;
    use SearchQuery;
    use PresentableTrait;
    protected $presenter = Presenter::class;
    protected $table = 'users';
    protected $fillable = ['name', 'username', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    /**
     * Cache all role titles
     *
     * @var array
     */
    protected static $roles;

    /**
     * @param string $role
     * @param mixed $default
     *
     * @return array|string
     */
    public static function roleTitles($role = null, $default = null)
    {
        if (is_null(self::$roles)) {
            self::$roles = [];
            $roles = config('user.roles');
            foreach ($roles as $name => $level) {
                self::$roles[$name] = trans("user::user.roles.{$name}");
            }
        }

        return $role ?
            (isset(self::$roles[$role]) ? self::$roles[$role] : $default) :
            self::$roles;
    }

    /**
     * @return array
     */
    public static function guardedRoleTitles()
    {
        $titles = self::roleTitles();
        unset($titles['super_admin']);

        return $titles;
    }

    /**
     * @param string $attribute
     * @param string $key
     *
     * @return array
     */
    public static function getList($attribute = 'title', $key = 'id')
    {
        return static::pluck($attribute, $key)->all();
    }


    /**
     * id đã mã hóa, $user->code
     *
     * @return string
     */
    public function getCodeAttribute()
    {
        return encode_id($this->id, 'user');
    }

    /**
     * @param string $code
     *
     * @return int
     */
    public static function getIdByCode($code)
    {
        return decode_id($code, 'user');
    }

    /**
     * @param string $code
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection|null|static
     */
    public static function findByCode($code, $columns = ['*'])
    {
        return static::find(static::getIdByCode($code), $columns);
    }

    /**
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        // Bỏ qua password trống
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Admin luôn đứng đầu
     * Chú ý gọi query này trước các quyery orderBy khác
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAdminFirst($query)
    {
        return $query->orderByRaw("`users`.`role`='super_admin' DESC")
            ->orderByRaw("`users`.`role`='admin' DESC");
    }

    /**
     * Mặc định không kiểm tra 'chính xác',
     * Thì role có level cao hơn sẽ 'bao gồm' role thấp
     *
     * @param string $role
     * @param bool $exact
     *
     * @return bool
     */
    public function is($role, $exact = false)
    {
        if ($role && $this->exists && $this->role) {
            if ($this->role == $role) {
                return true;
            } else {
                return !$exact &&
                (config("user.roles.{$this->role}") > config("user.roles.{$role}"));
            }
        }

        return false;
    }

    /**
     * Hàm 'động' kiểm tra role, vd:
     * $user->role = 'editor', check: $user->isEditor() => true
     * $user->role = 'super_admin', check: $user->isSuperAdmin() => true
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return starts_with($method, 'is') ?
            $this->is(snake_case(substr($method, 2)), $parameters ? $parameters[0] : false) :
            parent::__call($method, $parameters);
    }
}
