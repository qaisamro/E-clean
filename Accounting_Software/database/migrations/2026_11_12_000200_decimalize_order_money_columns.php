<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convert every money-related column in the POS to DECIMAL(12,2) so the
 * checkout and accounting can carry halalas/cents (صفرة وبنيمين / 2 خانات).
 * SQLite has no ALTER COLUMN, so affected tables are rebuilt.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('CREATE TABLE "orders_new" (
                "id" integer primary key autoincrement not null,
                "order_code" varchar not null,
                "payment_type" varchar check ("payment_type" in (\'1\',\'2\',\'3\',\'4\')) not null,
                "total_price" numeric,
                "paid_money" numeric,
                "remaining_money" numeric,
                "has_deferred_payment" tinyint(1) not null default \'0\',
                "status" varchar check ("status" in (\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\')) not null default \'1\',
                "payment_status" varchar check ("payment_status" in (\'1\',\'2\',\'3\')) not null default \'2\',
                "customer_id" integer,
                "user_id" integer,
                "created_by_customer" tinyint(1) not null default \'0\',
                "deliver_date" date,
                "created_at" datetime,
                "updated_at" datetime,
                "discount" numeric not null default \'0\',
                "notes" text,
                "deliver_time" varchar,
                "actual_delivery_date" date,
                foreign key("customer_id") references "customers"("id"),
                foreign key("user_id") references "users"("id")
            )');
            DB::statement('INSERT INTO "orders_new" SELECT * FROM "orders"');

            DB::statement('CREATE TABLE "order_details_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "item_id" integer,
                "service_id" integer,
                "price" numeric,
                "quantity" numeric not null,
                "total_price" numeric,
                "is_payment_deferred" tinyint(1) not null default \'0\',
                "description" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete set null,
                foreign key("service_id") references "services"("id") on delete set null
            )');
            DB::statement('INSERT INTO "order_details_new" SELECT * FROM order_details');

            DB::statement('CREATE TABLE "item_service_new" (
                "id" integer primary key autoincrement not null,
                "service_id" integer not null,
                "item_id" integer not null,
                "price" numeric,
                "note" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("service_id") references "services"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete cascade
            )');
            DB::statement('INSERT INTO "item_service_new" SELECT * FROM item_service');

            DB::statement('CREATE TABLE "payments_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "amount" numeric not null,
                "payment_method" varchar not null default \'cash\',
                "created_by" integer,
                "notes" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("created_by") references "users"("id") on delete set null
            )');
            DB::statement('INSERT INTO "payments_new" SELECT * FROM payments');

            DB::statement('CREATE TABLE "order_products_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "product_id" integer not null,
                "quantity" numeric not null,
                "price" numeric not null,
                "total_price" numeric not null,
                "note" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("product_id") references "products"("id") on delete cascade
            )');
            DB::statement('INSERT INTO "order_products_new" SELECT * FROM order_products');

            DB::statement('DROP TABLE "order_products"');
            DB::statement('DROP TABLE "payments"');
            DB::statement('DROP TABLE "item_service"');
            DB::statement('DROP TABLE "order_details"');
            DB::statement('DROP TABLE "orders"');

            DB::statement('ALTER TABLE "orders_new" RENAME TO "orders"');
            DB::statement('ALTER TABLE "order_details_new" RENAME TO "order_details"');
            DB::statement('ALTER TABLE "item_service_new" RENAME TO "item_service"');
            DB::statement('ALTER TABLE "payments_new" RENAME TO "payments"');
            DB::statement('ALTER TABLE "order_products_new" RENAME TO "order_products"');

            DB::statement('CREATE UNIQUE INDEX "order_details_order_id_item_id_quantity_unique" ON "order_details" ("order_id", "item_id", "quantity")');
            DB::statement('CREATE UNIQUE INDEX "item_service_item_id_service_id_unique" ON "item_service" ("item_id", "service_id")');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('CREATE TABLE "orders_new" (
                "id" integer primary key autoincrement not null,
                "order_code" varchar not null,
                "payment_type" varchar check ("payment_type" in (\'1\',\'2\',\'3\',\'4\')) not null,
                "total_price" integer,
                "paid_money" integer,
                "remaining_money" integer,
                "has_deferred_payment" tinyint(1) not null default \'0\',
                "status" varchar check ("status" in (\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\')) not null default \'1\',
                "payment_status" varchar check ("payment_status" in (\'1\',\'2\',\'3\')) not null default \'2\',
                "customer_id" integer,
                "user_id" integer,
                "created_by_customer" tinyint(1) not null default \'0\',
                "deliver_date" date,
                "created_at" datetime,
                "updated_at" datetime,
                "discount" integer not null default \'0\',
                "notes" text,
                "deliver_time" varchar,
                "actual_delivery_date" date,
                foreign key("customer_id") references "customers"("id"),
                foreign key("user_id") references "users"("id")
            )');
            DB::statement('INSERT INTO "orders_new" SELECT * FROM "orders"');

            DB::statement('CREATE TABLE "order_details_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "item_id" integer,
                "service_id" integer,
                "price" integer,
                "quantity" integer not null,
                "total_price" integer,
                "is_payment_deferred" tinyint(1) not null default \'0\',
                "description" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete set null,
                foreign key("service_id") references "services"("id") on delete set null
            )');
            DB::statement("INSERT INTO \"order_details_new\" SELECT \"id\", \"order_id\", \"item_id\", \"service_id\", CAST(ROUND(\"price\") AS INTEGER), CAST(ROUND(\"quantity\") AS INTEGER), CAST(ROUND(\"total_price\") AS INTEGER), \"is_payment_deferred\", \"description\", \"created_at\", \"updated_at\" FROM \"order_details\"");

            DB::statement('CREATE TABLE "item_service_new" (
                "id" integer primary key autoincrement not null,
                "service_id" integer not null,
                "item_id" integer not null,
                "price" integer,
                "note" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("service_id") references "services"("id") on delete cascade,
                foreign key("item_id") references "products"("id") on delete cascade
            )');
            DB::statement("INSERT INTO \"item_service_new\" SELECT \"id\", \"service_id\", \"item_id\", CAST(ROUND(\"price\") AS INTEGER), \"note\", \"created_at\", \"updated_at\" FROM \"item_service\"");

            DB::statement('CREATE TABLE "payments_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "amount" integer not null,
                "payment_method" varchar not null default \'cash\',
                "created_by" integer,
                "notes" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("created_by") references "users"("id") on delete set null
            )');
            DB::statement("INSERT INTO \"payments_new\" SELECT \"id\", \"order_id\", CAST(ROUND(\"amount\") AS INTEGER), \"payment_method\", \"created_by\", \"notes\", \"created_at\", \"updated_at\" FROM \"payments\"");

            DB::statement('CREATE TABLE "order_products_new" (
                "id" integer primary key autoincrement not null,
                "order_id" integer not null,
                "product_id" integer not null,
                "quantity" numeric not null,
                "price" integer not null,
                "total_price" integer not null,
                "note" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("order_id") references "orders_new"("id") on delete cascade,
                foreign key("product_id") references "products"("id") on delete cascade
            )');
            DB::statement("INSERT INTO \"order_products_new\" SELECT \"id\", \"order_id\", \"product_id\", \"quantity\", CAST(ROUND(\"price\") AS INTEGER), CAST(ROUND(\"total_price\") AS INTEGER), \"note\", \"created_at\", \"updated_at\" FROM \"order_products\"");

            DB::statement('DROP TABLE "order_products"');
            DB::statement('DROP TABLE "payments"');
            DB::statement('DROP TABLE "item_service"');
            DB::statement('DROP TABLE "order_details"');
            DB::statement('DROP TABLE "orders"');

            DB::statement('ALTER TABLE "orders_new" RENAME TO "orders"');
            DB::statement('ALTER TABLE "order_details_new" RENAME TO "order_details"');
            DB::statement('ALTER TABLE "item_service_new" RENAME TO "item_service"');
            DB::statement('ALTER TABLE "payments_new" RENAME TO "payments"');
            DB::statement('ALTER TABLE "order_products_new" RENAME TO "order_products"');

            DB::statement('CREATE UNIQUE INDEX "order_details_order_id_item_id_quantity_unique" ON "order_details" ("order_id", "item_id", "quantity")');
            DB::statement('CREATE UNIQUE INDEX "item_service_item_id_service_id_unique" ON "item_service" ("item_id", "service_id")');
        });
    }
};
