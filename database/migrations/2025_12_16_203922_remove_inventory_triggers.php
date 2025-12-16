<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_log_inventory_change ON inventory');
        DB::unprepared('DROP FUNCTION IF EXISTS log_inventory_change()');
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_update_inventory_status ON inventory');
        DB::unprepared('DROP FUNCTION IF EXISTS update_inventory_status()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Function: Auto-update inventory status based on stock levels
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_inventory_status()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Check if expired first
                IF NEW.expiry_date IS NOT NULL AND NEW.expiry_date < CURRENT_DATE THEN
                    NEW.status = 'expired';
                -- Check if out of stock
                ELSIF NEW.current_stock = 0 THEN
                    NEW.status = 'out_of_stock';
                -- Check if low stock
                ELSIF NEW.current_stock <= NEW.minimum_stock THEN
                    NEW.status = 'low_stock';
                -- Otherwise in stock
                ELSE
                    NEW.status = 'in_stock';
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Update inventory status before insert or update
        DB::unprepared("
            CREATE TRIGGER trigger_update_inventory_status
                BEFORE INSERT OR UPDATE ON inventory
                FOR EACH ROW
                EXECUTE FUNCTION update_inventory_status();
        ");

        // Function: Log inventory changes to inventory_transactions
        DB::unprepared("
            CREATE OR REPLACE FUNCTION log_inventory_change()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Only log if stock actually changed
                IF OLD.current_stock != NEW.current_stock THEN
                    INSERT INTO inventory_transactions (
                        inventory_id,
                        performable_type,
                        performable_id,
                        transaction_type,
                        quantity,
                        notes,
                        created_at,
                        updated_at
                    ) VALUES (
                        NEW.id,
                        'App\\Models\\System',
                        NULL,
                        CASE 
                            WHEN NEW.current_stock > OLD.current_stock THEN 'restock'
                            WHEN NEW.current_stock < OLD.current_stock THEN 'usage'
                            ELSE 'adjustment'
                        END,
                        ABS(NEW.current_stock - OLD.current_stock),
                        'Auto-logged stock change from ' || OLD.current_stock || ' to ' || NEW.current_stock,
                        NOW(),
                        NOW()
                    );
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Log inventory changes after update
        DB::unprepared("
            CREATE TRIGGER trigger_log_inventory_change
                AFTER UPDATE ON inventory
                FOR EACH ROW
                EXECUTE FUNCTION log_inventory_change();
        ");
    }
};
