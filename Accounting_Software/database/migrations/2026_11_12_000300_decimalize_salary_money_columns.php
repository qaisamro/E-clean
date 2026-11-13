<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Same decimalization contract for the salary tables:
 * users.salary, salaries.value/basic_salary/deductions/bonuses.
 * SQLite has no ALTER COLUMN — tables are rebuilt. PRAGMA foreign_keys is set
 * outside the transaction because it is a no-op once a transaction is open.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::transaction(function () {
            DB::statement('CREATE TABLE "users_new" (
                "id" integer primary key autoincrement not null,
                "name" varchar not null,
                "phone" varchar not null,
                "password" varchar not null,
                "role" varchar check ("role" in (\'1\',\'2\',\'3\',\'4\')) not null,
                "status" varchar check ("status" in (\'1\',\'2\')) not null default \'1\',
                "remember_token" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                "job_title" varchar,
                "salary" numeric,
                "hire_date" date
            )');
            DB::statement('INSERT INTO "users_new" SELECT * FROM "users"');

            DB::statement('CREATE TABLE "salaries_new" (
                "id" integer primary key autoincrement not null,
                "value" numeric not null,
                "description" varchar,
                "user_id" integer not null,
                "created_at" datetime,
                "updated_at" datetime,
                "basic_salary" numeric,
                "deductions" numeric not null default \'0\',
                "bonuses" numeric not null default \'0\',
                "payment_date" date,
                "payment_status" varchar not null default \'paid\',
                foreign key("user_id") references "users_new"("id") on delete cascade
            )');
            DB::statement('INSERT INTO "salaries_new" SELECT * FROM "salaries"');

            DB::statement('DROP TABLE "salaries"');
            DB::statement('DROP TABLE "users"');

            DB::statement('ALTER TABLE "users_new" RENAME TO "users"');
            DB::statement('ALTER TABLE "salaries_new" RENAME TO "salaries"');

            DB::statement('CREATE UNIQUE INDEX "users_phone_unique" ON "users" ("phone")');
        });
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::transaction(function () {
            DB::statement('CREATE TABLE "users_new" (
                "id" integer primary key autoincrement not null,
                "name" varchar not null,
                "phone" varchar not null,
                "password" varchar not null,
                "role" varchar check ("role" in (\'1\',\'2\',\'3\',\'4\')) not null,
                "status" varchar check ("status" in (\'1\',\'2\')) not null default \'1\',
                "remember_token" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                "job_title" varchar,
                "salary" integer,
                "hire_date" date
            )');
            DB::statement("INSERT INTO \"users_new\" SELECT \"id\", \"name\", \"phone\", \"password\", \"role\", \"status\", \"remember_token\", \"created_at\", \"updated_at\", \"job_title\", CAST(ROUND(\"salary\") AS INTEGER), \"hire_date\" FROM \"users\"");

            DB::statement('CREATE TABLE "salaries_new" (
                "id" integer primary key autoincrement not null,
                "value" integer not null,
                "description" varchar,
                "user_id" integer not null,
                "created_at" datetime,
                "updated_at" datetime,
                "basic_salary" integer,
                "deductions" integer not null default \'0\',
                "bonuses" integer not null default \'0\',
                "payment_date" date,
                "payment_status" varchar not null default \'paid\',
                foreign key("user_id") references "users_new"("id") on delete cascade
            )');
            DB::statement("INSERT INTO \"salaries_new\" SELECT \"id\", CAST(ROUND(\"value\") AS INTEGER), \"description\", \"user_id\", \"created_at\", \"updated_at\", CAST(ROUND(\"basic_salary\") AS INTEGER), CAST(ROUND(\"deductions\") AS INTEGER), CAST(ROUND(\"bonuses\") AS INTEGER), \"payment_date\", \"payment_status\" FROM \"salaries\"");

            DB::statement('DROP TABLE "salaries"');
            DB::statement('DROP TABLE "users"');

            DB::statement('ALTER TABLE "users_new" RENAME TO "users"');
            DB::statement('ALTER TABLE "salaries_new" RENAME TO "salaries"');

            DB::statement('CREATE UNIQUE INDEX "users_phone_unique" ON "users" ("phone")');
        });
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
