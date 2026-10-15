<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GearApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji 1 (CPMK1 Soal 1a & 1c): Endpoint selain login ditolak dengan 401 jika tanpa token.
     */
    public function test_cannot_access_gears_endpoint_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/gears');

        $response->assertStatus(401);
    }

    /**
     * Uji 2 (CPMK1 Soal 1a): Login sukses menghasilkan token autentikasi.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'abel@hongfilms.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'abel@hongfilms.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    /**
     * Uji 3 (CPMK1 Soal 1c): Validasi login gagal mengembalikan status 422.
     */
    public function test_login_validation_failure_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Uji 4 (CPMK1 Soal 1b & 1c): Tambah data baru mengembalikan status 201 dan tersimpan di DB.
     */
    public function test_can_create_gear_with_valid_token_returns_201(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Sony FX3 Cinema Line',
            'category' => 'Camera',
            'serial_number' => 'CAM-FX3-999',
            'daily_rate' => 850000.00,
            'status' => 'available',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/gears', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.serial_number', 'CAM-FX3-999');

        $this->assertDatabaseHas('gears', [
            'serial_number' => 'CAM-FX3-999',
        ]);
    }

    /**
     * Uji 5 (CPMK1 Soal 1c): Validasi tambah data gagal mengembalikan status 422.
     */
    public function test_create_gear_validation_failure_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/gears', [
            'name' => '',
            'category' => '',
            'serial_number' => '',
            'daily_rate' => -50,
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'category', 'serial_number', 'daily_rate', 'status']);
    }

    /**
     * Uji 6 (CPMK1 Soal 1b): Menampilkan seluruh daftar gear (CRUD - Read All).
     */
    public function test_can_list_all_gears(): void
    {
        $user = User::factory()->create();
        Gear::factory()->count(3)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/gears');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    }

    /**
     * Uji 7 (CPMK1 Soal 1b): Menampilkan detail satu gear (CRUD - Read One).
     */
    public function test_can_show_single_gear(): void
    {
        $user = User::factory()->create();
        $gear = Gear::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/gears/{$gear->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $gear->id);
    }

    /**
     * Uji 8 (CPMK1 Soal 1b): Memperbarui data gear (CRUD - Update).
     */
    public function test_can_update_gear(): void
    {
        $user = User::factory()->create();
        $gear = Gear::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/gears/{$gear->id}", [
            'status' => 'in_use',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.status', 'in_use');

        $this->assertDatabaseHas('gears', [
            'id' => $gear->id,
            'status' => 'in_use',
        ]);
    }

    /**
     * Uji 9 (CPMK1 Soal 1b): Menghapus data gear (CRUD - Delete).
     */
    public function test_can_delete_gear(): void
    {
        $user = User::factory()->create();
        $gear = Gear::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/gears/{$gear->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('gears', [
            'id' => $gear->id,
        ]);
    }

    /**
     * Uji 10 (CPMK1 Soal 1a): Logout mencabut token akses pengguna.
     */
    public function test_user_can_logout_and_revoke_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logout berhasil');

        $this->assertCount(0, $user->tokens);
    }
}
