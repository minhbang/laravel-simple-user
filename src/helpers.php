<?php
if (!function_exists('user')) {
    /**
     * Lấy user model: đã đăng nhập, hoặc có $id
     * Hoặc chỉ $attribute
     *
     * @param string|null $attribute
     * @param int|null $id
     *
     * @return \Minhbang\User\User|mixed|null
     */
    function user($attribute = null, $id = null)
    {
        if ($id) {
            $class = config('auth.providers.users.model');
            $user = $class::find($id);
        } else {
            $user = auth()->user();
        }
        return $user ? ($attribute ? $user->$attribute : $user) : null;
    }
}

if (!function_exists('user_is_author_of')) {
    /**
     * @param mixed $model
     * @param null|int $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    function user_is_author_of($model, $id = null)
    {
        $id = $id ?: user('id');
        return $id && ($id === $model->user_id);
    }
}

if (!function_exists('user_public_path')) {
    /**
     * Thư mục 'public' của user
     * Khi có lỗi, $ignore_error =
     * - true: return [eror code, message]
     * - false: dừng app bằng 'abort'
     *
     * @param string $path
     * @param bool $full path đầy đủ
     * @param bool $ignore_error
     * @param int|null $id
     *
     * @return string|array
     */
    function user_public_path($path = '', $full = false, $ignore_error = false, $id = null)
    {
        if ($code = user('code', $id)) {
            $path = '/' . setting('system.public_files') . "/$code" . ($path ? "/$path" : '');
            $path_full = public_path() . $path;
            return check_path($path_full, $ignore_error, $full ? $path_full : $path);
        } else {
            return response_error(3, 'Not call user_public_path() for Guest', $ignore_error);
        }
    }
}

if (!function_exists('user_storage_path')) {
    /**
     * Thư mục 'storage' của user
     * Khi có lỗi, $ignore_error =
     * - true: return [eror code, message]
     * - false: dừng app bằng 'abort'
     *
     * @param string $path
     * @param bool $ignore_error
     * @param int|null $id
     *
     * @return string|array
     */
    function user_storage_path($path = '', $ignore_error = false, $id = null)
    {
        if ($username = user('username', $id)) {
            $path = storage_path("data/{$username}" . ($path ? "/$path" : ''));
            return check_path($path, $ignore_error, $path);
        } else {
            return response_error(3, 'Not call user_storage_path() for Guest', $ignore_error);
        }
    }
}