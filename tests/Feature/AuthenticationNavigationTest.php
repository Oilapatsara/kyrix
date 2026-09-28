<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationNavigationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Isolate authentication from the application's MySQL-only historical migrations.
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    private function stubPage(string $route, string $method = 'index'): void
    {
        $controller = app('router')->getRoutes()->getByName($route)->getControllerClass();
        $this->mock($controller)->shouldReceive($method)->andReturn(response('Page reached'));
    }

    private function owner(): User
    {
        return User::create([
            'name' => 'Test Owner', 'email' => 'owner@example.test',
            'password' => Hash::make('test-password'), 'role' => 'owner', 'status' => 'active',
        ]);
    }

    private function customer(?User $user = null): Customer
    {
        return Customer::create([
            'user_id' => $user?->user_id,
            'first_name' => 'Test', 'last_name' => 'Customer',
            'email' => 'customer@example.test', 'password' => Hash::make('test-password'),
        ]);
    }

    public function test_owner_can_follow_navigation_and_notification_destinations(): void
    {
        $owner = $this->owner();
        $this->post('/login', ['email' => $owner->email, 'password' => 'test-password'])
            ->assertRedirect(route('owner.dashboard'));

        foreach (['owner.dashboard', 'owner.dresses.index', 'owner.bookings.index',
            'owner.payments.index', 'owner.returns.index', 'owner.customers.index', 'owner.reports.index'] as $route) {
            $this->stubPage($route);
            Auth::forgetGuards();
            $this->get(route($route))->assertOk();
            $this->assertAuthenticatedAs($owner);
        }

        $this->stubPage('owner.bookings.show', 'show');
        Auth::forgetGuards();
        $this->get(route('owner.bookings.show', 1))->assertOk();
        $this->get(route('owner.returns.index', ['tab' => 'overdue']))->assertOk();
        $this->assertAuthenticatedAs($owner);
    }

    public function test_login_page_keeps_an_authenticated_owner_on_the_dashboard(): void
    {
        $this->actingAs($this->owner())->get('/login')->assertRedirect(route('owner.dashboard'));
    }

    public function test_owner_visiting_customer_link_is_not_sent_to_login(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner)->get('/profile')->assertRedirect(route('owner.dashboard'));
        $this->assertAuthenticatedAs($owner);
    }

    public function test_legacy_customer_login_replaces_the_previous_owner_identity(): void
    {
        $this->actingAs($this->owner());
        $customer = $this->customer();
        $this->post('/login', ['email' => $customer->email, 'password' => 'test-password'])
            ->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer->fresh()->user);

        foreach (['profile.index', 'rentals.index'] as $route) {
            $this->stubPage($route);
            Auth::forgetGuards();
            $this->get(route($route))->assertOk();
            $this->assertAuthenticatedAs($customer->fresh()->user);
        }
    }

    public function test_failed_login_does_not_clear_an_existing_customer_session(): void
    {
        $customer = $this->customer();
        $this->withSession(['customer_logged_in' => true, 'customer_id' => $customer->customer_id])
            ->post('/login', ['email' => $customer->email, 'password' => 'incorrect'])
            ->assertSessionHas('customer_id', $customer->customer_id)
            ->assertSessionHas('customer_logged_in', true);
    }

    public function test_guests_still_need_to_login(): void
    {
        $this->get('/owner/returns')->assertRedirect(route('login'));
        $this->get('/profile')->assertRedirect(route('login'));
    }

    public function test_customer_login_and_navigation_keep_the_same_identity(): void
    {
        $user = User::create([
            'name' => 'Test Customer', 'email' => 'customer@example.test',
            'password' => Hash::make('test-password'), 'role' => 'customer', 'status' => 'active',
        ]);
        $customer = $this->customer($user);
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])
            ->assertRedirect(route('customer.dashboard'));
        $this->stubPage('profile.index');
        Auth::forgetGuards();
        $this->get('/profile')->assertOk()->assertSessionHas('customer_id', $customer->customer_id);
        $this->get('/login')->assertRedirect(route('customer.dashboard'));
        $this->get('/owner/returns')->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_session_is_restored_from_the_authenticated_account(): void
    {
        $user = User::create([
            'name' => 'Test Customer', 'email' => 'customer@example.test',
            'password' => Hash::make('test-password'), 'role' => 'customer', 'status' => 'active',
        ]);
        $customer = $this->customer($user);
        $this->stubPage('profile.index');
        $this->actingAs($user)->withSession(['customer_id' => 999, 'customer_logged_in' => true])
            ->get('/profile')->assertOk()->assertSessionHas('customer_id', $customer->customer_id);
    }

    public function test_registration_switches_the_guard_to_the_new_customer(): void
    {
        $this->actingAs($this->owner())->post('/register', [
            'name' => 'New Customer', 'email' => 'new@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs(User::where('email', 'new@example.test')->firstOrFail());
    }

    public function test_inactive_customer_cannot_recover_access_from_a_legacy_session(): void
    {
        $user = User::create([
            'name' => 'Inactive Customer', 'email' => 'customer@example.test',
            'password' => Hash::make('test-password'), 'role' => 'customer', 'status' => 'inactive',
        ]);
        $customer = $this->customer($user);
        $this->withSession(['customer_id' => $customer->customer_id, 'customer_logged_in' => true])
            ->get('/profile')->assertForbidden();
        $this->assertGuest();
    }

    public function test_explicit_logout_clears_both_account_states(): void
    {
        $this->actingAs($this->owner())->withSession(['customer_id' => 1, 'customer_logged_in' => true])
            ->post('/logout')->assertRedirect(route('home'))
            ->assertSessionMissing('customer_id')->assertSessionMissing('customer_logged_in');
        Auth::forgetGuards();
        $this->get('/owner/returns')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
