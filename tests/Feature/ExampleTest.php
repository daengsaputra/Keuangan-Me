<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk()->assertSee('Selamat datang kembali.');
    }

    public function test_a_transaction_can_be_created(): void
    {
        $response = $this->post('/transaksi', [
            'type' => 'income',
            'category' => 'Gaji',
            'description' => 'Gaji Juli',
            'amount' => 10000000,
            'transaction_date' => '2026-07-18',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('transactions', ['description' => 'Gaji Juli']);
    }

    public function test_invalid_transaction_is_rejected(): void
    {
        $response = $this->from('/transaksi')->post('/transaksi', [
            'type' => 'invalid',
            'amount' => -1,
        ]);

        $response->assertRedirect('/transaksi');
        $response->assertSessionHasErrors(['type', 'category', 'description', 'amount', 'transaction_date']);
        $this->assertSame(0, Transaction::count());
    }

    public function test_all_finance_pages_render(): void
    {
        $this->get('/transaksi')->assertOk()->assertSee('Catat transaksi.');
        $this->get('/laporan/bulanan')->assertOk()->assertSee('LAPORAN BULANAN');
        $this->get('/laporan/tahunan')->assertOk()->assertSee('LAPORAN TAHUNAN');
    }
}
