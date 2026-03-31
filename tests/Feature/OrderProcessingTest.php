<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\OrderMaster;
use App\Models\RestaurantTable;

class OrderProcessingTest extends TestCase
{
    // Note: RefreshDatabase might be destructive depending on setup. 
    // Using it here for logic validation.
    
    public function test_full_order_flow()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);
        
        $branch = Branch::create(['name' => 'Test Branch', 'status' => 1]);
        $table = RestaurantTable::create(['name' => 'Table 1', 'branch_id' => $branch->id, 'status' => 1]);
        $product = ProductItem::create(['name' => 'Pizza', 'price' => 15.00, 'status' => 1]);

        // 1. Create Order
        $response = $this->post(route('admin.orders.store'), [
            'branch_id' => $branch->id,
            'order_type' => 1,
            'table_id' => $table->id,
            'items' => [
                ['item_id' => $product->id, 'quantity' => 2, 'price' => 15.00]
            ],
            'discount' => 0
        ]);

        $order = OrderMaster::first();
        $this->assertEquals(30.00, $order->total_amount);
        $this->assertEquals(0, $table->fresh()->status); // Occupied

        // 2. Process Partial Payment
        $this->post(route('admin.orders.payments.store', $order->id), [
            'amount' => 10.00,
            'payment_method' => 1
        ]);

        $this->assertEquals(20.00, $order->fresh()->due_amount);

        // 3. Process Full Payment
        $this->post(route('admin.orders.payments.store', $order->id), [
            'amount' => 20.00,
            'payment_method' => 2
        ]);

        $this->assertEquals(0, $order->fresh()->due_amount);
        $this->assertEquals(4, $order->fresh()->order_status); // Completed
        $this->assertEquals(1, $table->fresh()->status); // Free
    }
}
