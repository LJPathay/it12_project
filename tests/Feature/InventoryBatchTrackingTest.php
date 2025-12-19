<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Inventory;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class InventoryBatchTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    /** @test */
    public function adding_inventory_creates_transaction_and_batch_with_balances()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.inventory.add'), [
                'item_name' => 'Test Medicine',
                'category' => 'Medicines',
                'current_stock' => 30,
                'minimum_stock' => 10,
                'unit' => 'pieces',
            ]);

        $response->assertStatus(302);
        
        $inventory = Inventory::first();
        $this->assertEquals(30, $inventory->current_stock);

        $transaction = InventoryTransaction::where('inventory_id', $inventory->id)->first();
        $this->assertEquals(0, $transaction->balance_before);
        $this->assertEquals(30, $transaction->balance_after);

        $batch = InventoryBatch::where('inventory_id', $inventory->id)->first();
        $this->assertEquals(0, $batch->previous_stock);
        $this->assertEquals(30, $batch->total_stock_after);
        $this->assertEquals(30, $batch->quantity);
    }

    /** @test */
    public function restocking_inventory_creates_new_batch_and_tracks_balances()
    {
        // Initial setup
        $inventory = Inventory::create([
            'item_name' => 'Test Medicine',
            'category' => 'Medicines',
            'current_stock' => 30,
            'minimum_stock' => 10,
            'unit' => 'pieces',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.inventory.restock', $inventory), [
                'quantity' => 100,
                'notes' => 'Restocked 100',
            ]);

        $response->assertStatus(302);
        
        $inventory->refresh();
        $this->assertEquals(130, $inventory->current_stock);

        // Check transaction
        $transaction = InventoryTransaction::where('inventory_id', $inventory->id)
            ->where('transaction_type', 'restock')
            ->orderBy('id', 'desc')
            ->first();
        $this->assertEquals(30, $transaction->balance_before);
        $this->assertEquals(130, $transaction->balance_after);

        // Check batch
        $batch = InventoryBatch::where('inventory_id', $inventory->id)
            ->orderBy('id', 'desc')
            ->first();
        $this->assertEquals(30, $batch->previous_stock);
        $this->assertEquals(130, $batch->total_stock_after);
        $this->assertEquals(100, $batch->quantity);
    }

    /** @test */
    public function deducting_inventory_decreases_batch_quantities_fifo()
    {
        // Initial setup with two batches
        $inventory = Inventory::create([
            'item_name' => 'Test Medicine',
            'category' => 'Medicines',
            'current_stock' => 0,
            'minimum_stock' => 10,
            'unit' => 'pieces',
        ]);

        // Batch 1: 50 units
        InventoryBatch::create([
            'inventory_id' => $inventory->id,
            'batch_number' => 'BATCH-1',
            'quantity' => 50,
            'remaining_quantity' => 50,
            'previous_stock' => 0,
            'total_stock_after' => 50,
            'expiry_date' => now()->addMonths(6),
            'received_date' => now()->subDays(10),
        ]);

        // Batch 2: 50 units
        InventoryBatch::create([
            'inventory_id' => $inventory->id,
            'batch_number' => 'BATCH-2',
            'quantity' => 50,
            'remaining_quantity' => 50,
            'previous_stock' => 50,
            'total_stock_after' => 100,
            'expiry_date' => now()->addMonths(12),
            'received_date' => now()->subDays(5),
        ]);

        $inventory->update(['current_stock' => 100]);

        // Deduct 70 units
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.inventory.deduct', $inventory), [
                'quantity' => 70,
                'notes' => 'Deducted 70',
            ]);

        $response->assertStatus(302);
        
        $inventory->refresh();
        $this->assertEquals(30, $inventory->current_stock);

        // Batch 1 should be exhausted (0 remaining)
        $batch1 = InventoryBatch::where('batch_number', 'BATCH-1')->first();
        $this->assertEquals(0, $batch1->remaining_quantity);

        // Batch 2 should have 30 remaining (50 - (70-50) = 30)
        $batch2 = InventoryBatch::where('batch_number', 'BATCH-2')->first();
        $this->assertEquals(30, $batch2->remaining_quantity);
    }
}
