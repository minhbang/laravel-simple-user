<?php
return [
    /**
     * Danh sách tất cả roles, định dạng: 'name' => level
     * Role có level cao hơn sẽ kế thừa 'quyền' của role thấp hơn
     */
    'roles'       => [
        'super_admin' => 100,
        'admin'       => 10,
    ],
    /**
     * Tự động add các route
     */
    'add_route'   => true,
    /**
     * Khai báo middlewares cho các Controller
     */
    'middlewares' => [
        // backend, mặc định đã có middleware 'auth' (phải đăng nhập trước)
        'user' => 'role:admin',
    ],
];