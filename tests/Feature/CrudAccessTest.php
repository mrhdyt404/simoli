<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class CrudAccessTest extends TestCase
{
    public function test_admin_user_can_access_crud_index_and_create_routes(): void
    {
        $admin = new User([
            'username' => 'admin_test',
            'level_akses' => 'admin',
        ]);
        $admin->ID = 1;

        $response = $this->actingAs($admin)->get(route('monitoring-alat-berat.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $response = $this->actingAs($admin)->get(route('monitoring-alat-berat.create'));
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_unit_user_can_access_crud_index_and_create_routes(): void
    {
        $unit = new User([
            'username' => 'unit_test',
            'level_akses' => 'unit',
            'id_pks' => 1,
        ]);
        $unit->ID = 2;

        $response = $this->actingAs($unit)->get(route('monitoring-alat-berat.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $response = $this->actingAs($unit)->get(route('monitoring-alat-berat.create'));
        $this->assertNotEquals(403, $response->getStatusCode());
    }
}
