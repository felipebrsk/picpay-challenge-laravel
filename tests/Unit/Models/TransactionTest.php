<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use PHPUnit\Framework\TestCase;
use Tests\Traits\TestUnitModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionTest extends TestCase
{
    use TestUnitModels;

    /**
     * The model to be tested.
     *
     * @return string
     */
    protected function model(): string
    {
        return Transaction::class;
    }

    /**
     * Test the model fillable attributes.
     *
     * @return void
     */
    public function test_fillable(): void
    {
        $fillable = [
            'to_id',
            'amount',
            'status',
            'from_id',
        ];

        $this->verifyIfExistFillable($fillable);
    }

    /**
     * Test if the model uses the correctly traits.
     *
     * @return void
     */
    public function test_if_use_traits(): void
    {
        $traits = [
            HasFactory::class,
            SoftDeletes::class,
        ];

        $this->verifyIfUseTraits($traits);
    }

    /**
     * Test the model dates attributes.
     *
     * @return void
     */
    public function test_dates_attribute(): void
    {
        $dates = [
            'created_at',
            'updated_at',
        ];

        $this->verifyDates($dates);
    }

    /**
     * Test the model casts attributes.
     *
     * @return void
     */
    public function test_casts_attribute(): void
    {
        $casts = [
            'id' => 'int',
            'deleted_at' => 'datetime',
        ];

        $this->verifyCasts($casts);
    }

    /**
     * Test transaction types.
     *
     * @return void
     */
    public function test_transaction_types(): void
    {
        $this->assertEquals(Transaction::ADDITION_CONST_ID, 1);
        $this->assertEquals(Transaction::SUBTRACTION_CONST_ID, 2);
    }
}
