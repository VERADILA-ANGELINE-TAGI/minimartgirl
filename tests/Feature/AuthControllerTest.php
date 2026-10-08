<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_cashier_can_update_their_account_name(): void
    {
        $cashier = User::factory()->create(['name' => 'Kasir MINIMARTGIRL']);

        $response = $this->actingAs($cashier)
            ->from(route('transaction.create'))
            ->put(route('profile.name.update'), ['name' => 'Ayu Lestari']);

        $response->assertRedirect(route('transaction.create'));
        $response->assertSessionHas('status', 'Nama kasir berhasil diperbarui.');
        $this->assertDatabaseHas('users', [
            'id' => $cashier->id,
            'name' => 'Ayu Lestari',
        ]);

        $this->get(route('transaction.create'))
            ->assertSee('value="Ayu Lestari"', false);
    }

    public function test_cashier_name_is_required_and_is_not_updated_when_blank(): void
    {
        $cashier = User::factory()->create(['name' => 'Kasir MINIMARTGIRL']);

        $response = $this->actingAs($cashier)
            ->from(route('transaction.create'))
            ->put(route('profile.name.update'), ['name' => '']);

        $response->assertRedirectBackWithErrors([
            'name' => 'Nama kasir wajib diisi.',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $cashier->id,
            'name' => 'Kasir MINIMARTGIRL',
        ]);
    }

    public function test_guest_cannot_update_a_cashier_name(): void
    {
        $response = $this->put(route('profile.name.update'), ['name' => 'Ayu Lestari']);

        $response->assertRedirect(route('login'));
    }

    public function test_login_form_validation_messages_are_in_indonesian(): void
    {
        $response = $this->from(route('login'))
            ->post(route('login.process'), ['email' => '', 'password' => '']);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'alamat surel wajib diisi.',
            'password' => 'kata sandi wajib diisi.',
        ]);
    }

    public function test_guest_can_register_a_separate_cashier_account_with_a_unique_email(): void
    {
        User::factory()->create(['email' => 'kasir.pertama@minimartgirl.com']);

        $response = $this->post(route('register.store'), [
            'name' => 'Ayu Lestari',
            'email' => 'Ayu@MINIMARTGIRL.com',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ]);

        $cashier = User::query()->where('email', 'ayu@minimartgirl.com')->sole();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($cashier);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'id' => $cashier->id,
            'name' => 'Ayu Lestari',
            'email' => 'ayu@minimartgirl.com',
        ]);
        $this->assertTrue(Hash::check('Rahasia123', $cashier->password));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.process'), [
            'email' => 'AYU@MINIMARTGIRL.COM',
            'password' => 'Rahasia123',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($cashier);
    }

    public function test_registration_form_shows_individual_cashier_account_fields(): void
    {
        $this->get(route('register'))
            ->assertSee('Daftar kasir')
            ->assertSee('Nama kasir')
            ->assertSee('Alamat surel')
            ->assertSee('Konfirmasi kata sandi')
            ->assertSee('Sudah punya akun?');
    }

    public function test_registration_rejects_an_email_already_owned_by_another_cashier_regardless_of_case(): void
    {
        User::factory()->create(['email' => 'kasir@minimartgirl.com']);

        $response = $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Kasir Kedua',
                'email' => 'KASIR@MINIMARTGIRL.COM',
                'password' => 'Rahasia123',
                'password_confirmation' => 'Rahasia123',
            ]);

        $response->assertRedirectBackWithErrors([
            'email' => 'Alamat surel sudah digunakan akun lain.',
        ]);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_rejects_a_password_shorter_than_eight_characters(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Kasir Baru',
                'email' => 'baru@minimartgirl.com',
                'password' => 'singkat',
                'password_confirmation' => 'singkat',
            ]);

        $response->assertRedirectBackWithErrors([
            'password' => 'Kata sandi minimal 8 karakter.',
        ]);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_a_password_when_confirmation_does_not_match(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Kasir Baru',
                'email' => 'baru@minimartgirl.com',
                'password' => 'Rahasia123',
                'password_confirmation' => 'Rahasia456',
            ]);

        $response->assertRedirectBackWithErrors([
            'password' => 'Konfirmasi kata sandi tidak cocok.',
        ]);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
