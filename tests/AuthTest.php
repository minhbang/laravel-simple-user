<?php
//use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
use Minhbang\User\User;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @var \Minhbang\User\User;
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['name' => 'Test User Name', 'username' => 'user', 'password' => 'user']);
    }
    /**
     * Nhìn thấy from đăng nhập
     */
    public function testViewingLoginForm()
    {
        $this->visit('/auth/login')->see(trans('user::account.login_title'));
    }

    /**
     * Không nhập username và/hoặc password
     */
    public function testLoginInvalidInput()
    {
        $this->visit('/auth/login')
            ->submitForm(trans('user::account.login'))
            ->seePageIs('auth/login')
            ->see(trans('errors.input'));

        $this->visit('/auth/login')
            ->submitForm(trans('user::account.login'), ['username' => 'user'])
            ->seePageIs('auth/login')
            ->see(trans('errors.input'));

        // một cách khác tương tác với form
        $this->visit('/auth/login')
            ->type('user', 'password')
            ->press(trans('user::account.login'))
            ->seePageIs('auth/login')
            ->see(trans('errors.input'));
    }

    /**
     * Login thất bại
     */
    public function testLoginFail()
    {
        // user không tồn tại
        $this->visit('/auth/login')
            ->submitForm(trans('user::account.login'), ['username' => 'abc', 'password' => '111'])
            ->seePageIs('auth/login')
            ->see(trans('user::account.credentials_invalid'));

        // Sai mật khẩu
        $this->visit('/auth/login')
            ->submitForm(trans('user::account.login'), ['username' => 'user', 'password' => '111111'])
            ->seePageIs('auth/login')
            ->see(trans('user::account.credentials_invalid'));
    }

    /**
     * Đăng nhập thành công
     */
    public function testLoginSuccess()
    {
        // Đăng nhập
        $this->visit('/auth/login')
            ->submitForm(trans('user::account.login'), ['username' => 'user', 'password' => 'user'])
            ->seePageIs('/')
            ->see($this->user->name)
            ->assertNotTrue(is_null(Auth::user()));

        // Redirect nếu đã đăng nhập
        $this->visit('/auth/login')
            ->seePageIs('/')
            ->assertNotTrue(is_null(Auth::user()));
    }

    /**
     * Đăng xuất
     */
    public function testLogout()
    {
        $this->visit('/auth/logout')
            ->seePageIs('/')
            ->assertTrue(is_null(Auth::user()));
    }
}